<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAIService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.openai.key');
        $this->model = (string) config('services.openai.model', 'gpt-4o-mini');
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     */
    public function chat(array $messages): string
    {
        if (! $this->apiKey) {
            throw new RuntimeException('OPENAI_API_KEY belum diset di .env');
        }

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => $messages,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('OpenAI API error: '.$response->body());
        }

        return $response->json('choices.0.message.content') ?? '';
    }
}