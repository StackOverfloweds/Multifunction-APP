<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

/**
 * Agent "AI Engineer" — dijalankan via OpenRouter (bukan model lokal lagi).
 *
 * RemembersConversations otomatis simpan & muat riwayat chat dari tabel
 * agent_conversations / agent_conversation_messages (dibuat oleh migration
 * resmi laravel/ai), jadi kita TIDAK perlu tabel ai_conversations /
 * ai_messages custom lagi seperti implementasi Ollama sebelumnya.
 *
 * PENTING: jangan definisikan method messages() di sini — kalau ada,
 * itu akan menimpa perilaku otomatis RemembersConversations dan riwayat
 * percakapan tidak akan dimuat dari database.
 */
#[Provider(Lab::OpenRouter)]
#[Temperature(0.7)]
#[Timeout(120)]
class AiEngineerAgent implements Agent, Conversational
{
    use Promptable, RemembersConversations;

    /**
     * Instruksi sistem untuk agent ini.
     */
    public function instructions(): string
    {
        return 'Kamu adalah AI Engineer assistant di dalam sistem Multifunction App. '
            . 'Jawab singkat, jelas, dan teknis bila relevan. Gunakan Bahasa Indonesia '
            . 'kecuali user bertanya dalam bahasa lain.';
    }
}