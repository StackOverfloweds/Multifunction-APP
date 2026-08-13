<?php

namespace App\Jobs;

use App\Models\AiUsageLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LogAiUsage implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public string $userId,
        public ?string $conversationId,
        public ?int $aiModelId,
        public int $promptTokens,
        public int $completionTokens,
        public int $durationMs,
        public bool $isSuccess = true,
        public ?string $errorMessage = null,
    ) {}

    public function handle(): void
    {
        AiUsageLog::create([
            'user_id' => $this->userId,
            'conversation_id' => $this->conversationId,
            'ai_model_id' => $this->aiModelId,
            'prompt_tokens' => $this->promptTokens,
            'completion_tokens' => $this->completionTokens,
            'total_tokens' => $this->promptTokens + $this->completionTokens,
            'duration_ms' => $this->durationMs,
            'is_success' => $this->isSuccess,
            'error_message' => $this->errorMessage,
        ]);
    }
}