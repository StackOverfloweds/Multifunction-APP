<?php

namespace App\GraphQL\Queries;

use App\Models\Folder;

class FolderQuery
{
    /**
     * Setara FolderController::tree — kembalikan daftar folder flat
     * (id, parentId, name) supaya tree-nya dibangun di sisi Vue.
     */
    public function tree($_, array $args)
    {
        $user = auth('api')->user();

        $query = Folder::query()->select(['id', 'parent_id', 'name']);

        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy('name')->get()->map(fn (Folder $folder) => [
            'id' => $folder->id,
            'parentId' => $folder->parent_id,
            'name' => $folder->name,
        ]);
    }
}