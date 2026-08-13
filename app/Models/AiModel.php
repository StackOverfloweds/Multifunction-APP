<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiModel extends Model
{
    protected $fillable = [
        'name', 'slug', 'provider', 'endpoint',
        'context_length', 'temperature', 'config',
        'is_active', 'is_default',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];
}