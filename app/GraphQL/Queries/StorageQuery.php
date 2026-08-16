<?php

namespace App\GraphQL\Queries;

use App\Models\FileStorage;

class StorageQuery
{
    /**
     * Setara StorageController::index (bagian query file-nya). Breadcrumbs
     * tidak diikutkan di sini — itu murni state navigasi UI, biar dihitung
     * di sisi Vue dari folder yang lagi aktif + query `folder(id)` kalau perlu.
     */
    public function files($_, array $args): array
    {
        $user = auth('api')->user();

        $query = FileStorage::query()->with(['user', 'folder']);

        $folderId = $args['folderId'] ?? null;
        $query->where('folder_id', $folderId);

        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        if (! empty($args['search'])) {
            $search = $args['search'];
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'LIKE', "%{$search}%")
                    ->orWhere('mime_type', 'LIKE', "%{$search}%");
            });
        }

        $perPage = $args['first'] ?? 15;
        $page = $args['page'] ?? 1;

        $paginator = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'total' => $paginator->total(),
            'currentPage' => $paginator->currentPage(),
            'lastPage' => $paginator->lastPage(),
        ];
    }
}