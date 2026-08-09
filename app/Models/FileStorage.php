<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileStorage extends Model
{
    protected $fillable = [
        'user_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'file_hash',
        'storage_node',
        'visibility',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}