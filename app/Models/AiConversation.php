<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static Builder|AiConversation where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static AiConversation findOrFail($id)
 * @method static AiConversation create(array $attributes)
 */
class AiConversation extends Model
{
    use HasUlids, SoftDeletes;

    protected $fillable = [
        'id', 'user_id', 'ai_model_id', 'title',
        'is_archived', 'is_pinned', 'last_message_at',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'is_pinned' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aiModel()
    {
        return $this->belongsTo(AiModel::class);
    }

    public function messages()
    {
        return $this->hasMany(AiMessage::class, 'conversation_id');
    }
}