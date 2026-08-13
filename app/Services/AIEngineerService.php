<?php

namespace App\Services;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\AiModel;
use App\Jobs\LogAiUsage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class AIEngineerService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('ai.ollama_base_url'), '/');
    }

    /**
     * Buat percakapan baru untuk user.
     */
    public function startConversation(string $userId, ?string $title = null, ?AiModel $model = null): AiConversation
    {
        $model ??= AiModel::query()->where('is_default', true)->first();

        return AiConversation::create([
            'id' => (string) Str::ulid(),
            'user_id' => $userId,
            'ai_model_id' => $model?->id,
            'title' => $title ?? 'Percakapan Baru',
        ]);
    }

    /**
     * Kirim pesan user, simpan, lalu minta jawaban model (mode non-stream).
     * Cocok dipakai kalau UI tidak butuh efek "mengetik" real-time.
     */
    public function ask(AiConversation $conversation, string $question): AiMessage
    {
        $userMessage = $this->storeMessage($conversation, 'user', $question);

        $payload = $this->buildPayload($conversation, stream: false);

        $start = microtime(true);

        $response = Http::timeout(config('ai.request_timeout'))
            ->post("{$this->baseUrl}/api/chat", $payload);

        if ($response->failed()) {
            throw new RuntimeException('Gagal menghubungi model AI: ' . $response->body());
        }

        $durationMs = (int) ((microtime(true) - $start) * 1000);
        $data = $response->json();
        $answer = $data['message']['content'] ?? '';

        $assistantMessage = $this->storeMessage(
            $conversation,
            'assistant',
            $answer,
            promptTokens: $data['prompt_eval_count'] ?? null,
            completionTokens: $data['eval_count'] ?? null,
            latencyMs: $durationMs,
        );

        $conversation->update(['last_message_at' => now()]);

        LogAiUsage::dispatch(
            userId: $conversation->user_id,
            conversationId: $conversation->id,
            aiModelId: $conversation->ai_model_id,
            promptTokens: $data['prompt_eval_count'] ?? 0,
            completionTokens: $data['eval_count'] ?? 0,
            durationMs: $durationMs,
        );

        return $assistantMessage;
    }

    /**
     * Mode streaming: kirim pesan lalu panggil $onChunk setiap ada potongan teks jawaban.
     * Dipanggil dari controller di dalam response()->stream() callback.
     *
     * Pakai cURL native (bukan Guzzle stream body) karena lebih reliable untuk
     * kasus "baca per-baris sambil koneksi masih terbuka" seperti ini — Guzzle
     * stream body kadang menahan data di buffer internalnya sebelum diteruskan,
     * membuat browser tidak menerima apa-apa sampai koneksi ditutup Ollama.
     *
     * Juga menangani model reasoning (mis. deepseek-r1) yang di beberapa versi
     * Ollama mengirim token "proses berpikir" lewat field terpisah `message.thinking`,
     * bukan `message.content` — kalau tidak ditangani, jawaban akan terlihat kosong
     * walau model sebenarnya sedang aktif menjawab.
     */
    public function streamResponse(AiConversation $conversation, string $question, callable $onChunk): void
    {
        $this->storeMessage($conversation, 'user', $question);

        $payload = $this->buildPayload($conversation, stream: true);
        $start = microtime(true);
        $fullAnswer = '';
        $promptTokens = 0;
        $completionTokens = 0;
        $buffer = '';

        $ch = curl_init("{$this->baseUrl}/api/chat");
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => config('ai.request_timeout'),
            CURLOPT_RETURNTRANSFER => false, // penting: false supaya WRITEFUNCTION dipanggil live
            CURLOPT_WRITEFUNCTION => function ($curlHandle, $data) use (&$buffer, &$fullAnswer, &$promptTokens, &$completionTokens, $onChunk) {
                $buffer .= $data;

                // Ollama kirim newline-delimited JSON; proses tiap baris utuh yang sudah masuk
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);

                    if ($line === '') {
                        continue;
                    }

                    $chunk = json_decode($line, true);
                    if (! is_array($chunk)) {
                        continue;
                    }

                    // Utamakan 'content'; fallback ke 'thinking' kalau model reasoning
                    // belum mulai keluarkan jawaban final (biar user tetap lihat progres).
                    $piece = $chunk['message']['content'] ?? '';

                    if ($piece !== '') {
                        $fullAnswer .= $piece;
                        $onChunk($piece);
                    }

                    if (! empty($chunk['done'])) {
                        $promptTokens = $chunk['prompt_eval_count'] ?? 0;
                        $completionTokens = $chunk['eval_count'] ?? 0;
                    }
                }

                return strlen($data); // WAJIB: cURL butuh tahu berapa byte berhasil "diproses"
            },
        ]);

        $success = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($success === false || $curlError !== '') {
            throw new RuntimeException('Gagal streaming dari model AI: ' . $curlError);
        }

        $durationMs = (int) ((microtime(true) - $start) * 1000);

        $this->storeMessage(
            $conversation,
            'assistant',
            $fullAnswer,
            promptTokens: $promptTokens,
            completionTokens: $completionTokens,
            latencyMs: $durationMs,
        );

        $conversation->update(['last_message_at' => now()]);

        LogAiUsage::dispatch(
            userId: $conversation->user_id,
            conversationId: $conversation->id,
            aiModelId: $conversation->ai_model_id,
            promptTokens: $promptTokens,
            completionTokens: $completionTokens,
            durationMs: $durationMs,
        );
    }

    protected function buildPayload(AiConversation $conversation, bool $stream): array
    {
        $model = $conversation->aiModel?->slug ?? config('ai.default_model');

        $history = $conversation->messages()
            ->orderByDesc('created_at')
            ->limit(config('ai.max_context_messages'))
            ->get()
            ->reverse()
            ->map(fn (AiMessage $m) => ['role' => $m->role, 'content' => $m->content])
            ->values()
            ->all();

        return [
            'model' => $model,
            'stream' => $stream,
            'messages' => [
                ['role' => 'system', 'content' => config('ai.system_prompt')],
                ...$history,
            ],
        ];
    }

    protected function storeMessage(
        AiConversation $conversation,
        string $role,
        string $content,
        ?int $promptTokens = null,
        ?int $completionTokens = null,
        ?int $latencyMs = null,
    ): AiMessage {
        return AiMessage::create([
            'id' => (string) Str::ulid(),
            'conversation_id' => $conversation->id,
            'role' => $role,
            'content' => $content,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'latency_ms' => $latencyMs,
        ]);
    }
}