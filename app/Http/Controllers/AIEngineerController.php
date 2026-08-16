<?php

namespace App\Http\Controllers;

use App\Ai\Agents\AiEngineerAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Ai\Models\Conversation;

/**
 * API version of the former AIEngineerController.
 *
 * index(), store(), and destroy() HAVE BEEN MOVED to GraphQL
 * (ConversationQuery::list/find, ConversationMutator::start/delete).
 *
 * send() is INTENTIONALLY RETAINED as REST + SSE because standard
 * GraphQL does not support token-by-token streaming responses like this.
 *
 * Its route is registered manually in routes/api.php and protected by
 * the auth:api (JWT) middleware — no longer using the auth (session)
 * middleware as in the previous implementation.
 */
class AIEngineerController extends Controller
{
    public function send(Request $request, Conversation $conversation)
    {
        abort_unless(
            Auth::user()->conversations()->whereKey($conversation->id)->exists(),
            403
        );

        $request->validate([
            'message' => ['required', 'string', 'max:8000'],
        ]);

        if ($conversation->title === 'Percakapan Baru') {
            $conversation->update([
                'title' => $this->generateTitle($request->input('message')),
            ]);
        }

        set_time_limit(0);

        return (new AiEngineerAgent)
            ->continue($conversation->id, as: Auth::user())
            ->stream($request->input('message'))
            ->usingVercelDataProtocol();
    }

    private function generateTitle(string $message): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', $message));

        return Str::limit($clean, 50);
    }
}