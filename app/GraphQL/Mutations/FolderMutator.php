<?php

namespace App\GraphQL\Mutations;

use App\Models\Folder;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Storage;

class FolderMutator
{
    public function create($_, array $args): Folder
    {
        $user = auth('api')->user();

        if (! empty($args['parentId'])) {
            $parent = Folder::findOrFail($args['parentId']);
            $this->authorizeAccess($user, $parent);
        }

        return Folder::create([
            'user_id' => $user->id,
            'parent_id' => $args['parentId'] ?? null,
            'name' => trim($args['name']),
        ]);
    }

    public function rename($_, array $args): Folder
    {
        $user = auth('api')->user();
        $folder = Folder::findOrFail($args['id']);
        $this->authorizeAccess($user, $folder);

        $folder->update(['name' => trim($args['name'])]);

        return $folder;
    }

    public function move($_, array $args): Folder
    {
        $user = auth('api')->user();
        $folder = Folder::findOrFail($args['id']);
        $this->authorizeAccess($user, $folder);

        $newParentId = $args['parentId'] ?? null;

        if ($newParentId === $folder->id) {
            throw new Error('Folder tidak bisa dipindahkan ke dalam dirinya sendiri.');
        }

        if ($newParentId) {
            $newParent = Folder::findOrFail($newParentId);
            $this->authorizeAccess($user, $newParent);

            // cegah memindahkan ke dalam salah satu sub-foldernya sendiri (mencegah cycle)
            $ancestor = $newParent;
            while ($ancestor) {
                if ($ancestor->id === $folder->id) {
                    throw new Error('Tidak bisa memindahkan folder ke dalam sub-foldernya sendiri.');
                }
                $ancestor = $ancestor->parent;
            }
        }

        try {
            $folder->update(['parent_id' => $newParentId]);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new Error('Sudah ada folder dengan nama yang sama di tujuan tersebut.');
        }

        return $folder;
    }

    public function delete($_, array $args): bool
    {
        $user = auth('api')->user();

        if (! in_array($user->role, ['super_admin', 'admin'], true)) {
            throw new Error('Anda tidak memiliki akses untuk menghapus folder ini.');
        }

        $folder = Folder::findOrFail($args['id']);
        $this->deleteRecursive($folder);

        return true;
    }

    /**
     * Setara FolderController::resolvePath — dipakai fitur "Upload Folder"
     * (webkitdirectory) di frontend. Memastikan setiap level folder ada
     * (buat kalau belum ada), lalu mengembalikan id folder paling dalam.
     */
    public function resolvePath($_, array $args): ?string
    {
        $user = auth('api')->user();

        if (! empty($args['parentId'])) {
            $parent = Folder::findOrFail($args['parentId']);
            $this->authorizeAccess($user, $parent);
        }

        $segments = array_values(array_filter(
            explode('/', trim($args['path'], '/')),
            fn ($segment) => $segment !== ''
        ));

        $parentId = $args['parentId'] ?? null;
        $folder = null;

        foreach ($segments as $segment) {
            $folder = Folder::firstOrCreate([
                'user_id' => $user->id,
                'parent_id' => $parentId,
                'name' => mb_substr($segment, 0, 255),
            ]);
            $parentId = $folder->id;
        }

        return $folder?->id;
    }

    private function authorizeAccess($user, Folder $folder): void
    {
        if ($user->role === 'user' && $folder->user_id !== $user->id) {
            throw new Error('Anda tidak memiliki akses ke folder ini.');
        }
    }

    private function deleteRecursive(Folder $folder): void
    {
        foreach ($folder->children as $child) {
            $this->deleteRecursive($child);
        }

        foreach ($folder->files as $file) {
            if (Storage::exists($file->file_path)) {
                Storage::delete($file->file_path);
            }
            $file->delete($folder);
        }

        $folder->delete($folder);
    }
}