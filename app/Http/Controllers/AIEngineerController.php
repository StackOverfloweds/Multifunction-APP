<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Services\AIEngineerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AIEngineerController extends Controller
{
    public function __construct(protected AIEngineerService $ai) {}

    /**
     * Halaman utama chat (Blade).
     */
    public function index(Request $request)
    {
        $conversations = AiConversation::query()
            ->where('user_id', Auth::id())
            ->where('is_archived', false)
            ->orderByDesc('last_message_at')
            ->get();

        $activeConversation = null;

        if ($request->filled('conversation')) {
            $activeConversation = AiConversation::query()
                ->where('user_id', Auth::id())
                ->with('messages')
                ->findOrFail($request->query('conversation'));
        }

        return view('ai-engineer.index', compact('conversations', 'activeConversation'));
    }

    /**
     * Buat percakapan baru lalu redirect ke halaman chat.
     */
    public function store(Request $request)
    {
        $conversation = $this->ai->startConversation(Auth::id());

        return redirect()->route('ai-engineer.index', ['conversation' => $conversation->id]);
    }

    /**
     * Endpoint streaming (Server-Sent Events) — dipanggil via fetch() dari Blade/Alpine.
     */
    public function send(Request $request, AiConversation $conversation): StreamedResponse
    {
        abort_unless($conversation->user_id === Auth::id(), 403);

        $request->validate([
            'message' => ['required', 'string', 'max:8000'],
        ]);

        return response()->stream(function () use ($conversation, $request) {
            // matikan output buffering PHP kalau ada, biar setiap echo langsung terkirim
            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            $this->ai->streamResponse($conversation, $request->input('message'), function (string $piece) {
                echo 'data: ' . json_encode(['content' => $piece]) . "\n\n";
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            });

            echo "data: [DONE]\n\n";
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no', // penting agar Nginx tidak buffer SSE
        ]);
    }
}