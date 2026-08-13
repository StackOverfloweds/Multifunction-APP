<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class AiMessage extends Model
{
    use HasUlids;

    protected $fillable = [
        'id', 'conversation_id', 'role', 'content',
        'prompt_tokens', 'completion_tokens', 'latency_ms', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function conversation()
    {
        return $this->belongsTo(AiConversation::class, 'conversation_id');
    }
}