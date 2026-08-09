<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id')->orderBy('name');
    }

    public function files()
    {
        return $this->hasMany(FileStorage::class, 'folder_id');
    }

    /**
     * Kembalikan array breadcrumb dari root sampai folder ini sendiri.
     * Contoh: [Root-level folder, Sub folder, ..., $this]
     */
    public function breadcrumbs(): array
    {
        $trail = [];
        $node = $this;

        while ($node) {
            array_unshift($trail, $node);
            $node = $node->parent;
        }

        return $trail;
    }
}
