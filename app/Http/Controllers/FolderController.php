<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FolderController extends Controller
{
    /**
     * Buat folder baru (bisa di root atau di dalam folder lain).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'uuid', 'exists:folders,id'],
        ]);

        if (!empty($validated['parent_id'])) {
            $parent = Folder::findOrFail($validated['parent_id']);
            $this->authorizeAccess($user, $parent);
        }

        $folder = Folder::create([
            'user_id' => $user->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => trim($validated['name']),
        ]);

        return back()->with('success', "Folder \"{$folder->name}\" berhasil dibuat.");
    }

    /**
     * Ganti nama folder.
     */
    public function rename(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();
        $folder = Folder::findOrFail($id);
        $this->authorizeAccess($user, $folder);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $folder->update(['name' => trim($validated['name'])]);

        return back()->with('success', 'Folder berhasil diganti nama.');
    }

    /**
     * Hapus folder beserta seluruh isinya (sub folder & file), hanya admin/super_admin.
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();

        if (!in_array($user->role, ['super_admin', 'admin'], true)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus folder ini.');
        }

        $folder = Folder::findOrFail($id);
        $parentId = $folder->parent_id;

        $this->deleteRecursive($folder);

        return redirect()->route('storage.index', array_filter(['folder' => $parentId]))
            ->with('success', 'Folder berhasil dihapus beserta seluruh isinya.');
    }

    /**
     * Pindahkan folder ke induk lain (atau ke Root kalau parent_id dikosongkan).
     * Sub-folder & file di dalamnya ikut pindah otomatis karena tetap mereferensikan
     * folder ini sebagai induknya.
     */
    public function move(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $folder = Folder::findOrFail($id);
        $this->authorizeAccess($user, $folder);

        $validated = $request->validate([
            'parent_id' => ['nullable', 'uuid', 'exists:folders,id'],
        ]);

        $newParentId = $validated['parent_id'] ?? null;

        if ($newParentId === $folder->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Folder tidak bisa dipindahkan ke dalam dirinya sendiri.',
            ], 422);
        }

        if ($newParentId) {
            $newParent = Folder::findOrFail($newParentId);
            $this->authorizeAccess($user, $newParent);

            // cegah memindahkan ke dalam salah satu sub-foldernya sendiri (mencegah cycle)
            $ancestor = $newParent;
            while ($ancestor) {
                if ($ancestor->id === $folder->id) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tidak bisa memindahkan folder ke dalam sub-foldernya sendiri.',
                    ], 422);
                }
                $ancestor = $ancestor->parent;
            }
        }

        try {
            $folder->update(['parent_id' => $newParentId]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sudah ada folder dengan nama yang sama di tujuan tersebut.',
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Folder \"{$folder->name}\" berhasil dipindahkan.",
        ]);
    }

    /**
     * Daftar seluruh folder milik user (flat) untuk dipakai membangun dropdown
     * "pindahkan ke folder" di frontend. Admin/super_admin melihat semua folder,
     * user biasa hanya melihat foldernya sendiri (konsisten dengan index()).
     */
    public function tree(Request $request): JsonResponse
{
    $user = $request->user();

    /** @var \Illuminate\Database\Eloquent\Builder $query */
    $query = Folder::query();

    if ($user->role === 'user') {
        $query->where('user_id', $user->id);
    }

    $folders = $query->orderBy('name')->get(['id', 'parent_id', 'name']);

    return response()->json(['folders' => $folders]);
}

    /**
     * Dipakai oleh fitur "Upload Folder" (webkitdirectory) di frontend.
     * Menerima path relatif, misal "Laporan/2026/Januari", lalu memastikan
     * setiap level folder ada (buat kalau belum ada), dan mengembalikan
     * id folder paling dalam (leaf) agar file bisa langsung diarahkan ke sana.
     */
    public function resolvePath(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'path' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'uuid', 'exists:folders,id'],
        ]);

        if (!empty($validated['parent_id'])) {
            $parent = Folder::findOrFail($validated['parent_id']);
            $this->authorizeAccess($user, $parent);
        }

        $segments = array_values(array_filter(
            explode('/', trim($validated['path'], '/')),
            fn ($segment) => $segment !== ''
        ));

        $parentId = $validated['parent_id'] ?? null;
        $folder = null;

        foreach ($segments as $segment) {
            $folder = Folder::firstOrCreate([
                'user_id' => $user->id,
                'parent_id' => $parentId,
                'name' => mb_substr($segment, 0, 255),
            ]);
            $parentId = $folder->id;
        }

        return response()->json([
            'folder_id' => $folder?->id,
        ]);
    }

    private function authorizeAccess($user, Folder $folder): void
    {
        if ($user->role === 'user' && $folder->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke folder ini.');
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
