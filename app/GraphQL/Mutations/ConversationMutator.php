<?php

namespace App\GraphQL\Mutations;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\OpenAIService;
use GraphQL\Error\Error;

class ConversationMutator
{
    public function __construct(protected OpenAIService $openAI)
    {
    }

    public function start($_, array $args): Conversation
    {
        $user = auth('api')->user();

        return Conversation::create([
            'user_id' => $user->id,
            'title' => $args['title'] ?? 'Percakapan baru',
        ]);
    }

    public function sendMessage($_, array $args): Message
    {
        $user = auth('api')->user();

        $conversation = isset($args['conversationId'])
            ? Conversation::where('id', $args['conversationId'])
                ->where('user_id', $user->id)
                ->firstOrFail()
            : Conversation::create([
                'user_id' => $user->id,
                'title' => str($args['content'])->limit(40),
            ]);

        $conversation->messages()->create([
            'role' => 'user',
            'content' => $args['content'],
        ]);

        $history = $conversation->messages()
            ->orderBy('created_at')
            ->get(['role', 'content'])
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        try {
            $reply = $this->openAI->chat($history);
        } catch (\Throwable $e) {
            throw new Error('Gagal menghubungi AI provider: '.$e->getMessage());
        }

        return $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $reply,
        ]);
    }
}