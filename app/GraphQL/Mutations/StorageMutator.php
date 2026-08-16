<?php

namespace App\GraphQL\Mutations;

use App\Models\FileStorage;
use App\Models\Folder;
use GraphQL\Error\Error;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageMutator
{
    /**
     * Setara StorageController::storeChunk — terima 1 chunk, assemble kalau
     * semua chunk sudah lengkap. Logic byte-for-byte sama seperti aslinya.
     */
    public function uploadChunk($_, array $args): array
    {
        $user = auth('api')->user();

        /** @var UploadedFile $uploadedChunk */
        $uploadedChunk = $args['file'];
        $uploadId = $args['uploadId'];
        $chunkIndex = $args['chunkIndex'];
        $totalChunks = $args['totalChunks'];
        $filename = $args['filename'];
        $folderId = $args['folderId'] ?? null;

        if ($chunkIndex >= $totalChunks) {
            throw new Error('chunkIndex harus lebih kecil dari totalChunks.');
        }

        if ($folderId) {
            $folder = Folder::findOrFail($folderId);
            if ($user->role === 'user' && $folder->user_id !== $user->id) {
                throw new Error('Anda tidak memiliki akses ke folder ini.');
            }
        }

        $tempDirectory = "storage/temp/{$uploadId}";

        $uploadedChunk->storeAs($tempDirectory, "part_{$chunkIndex}");

        clearstatcache(true);

        $missingChunks = [];
        for ($i = 0; $i < $totalChunks; $i++) {
            if (! Storage::exists("{$tempDirectory}/part_{$i}")) {
                $missingChunks[] = $i;
            }
        }
        $received = $totalChunks - count($missingChunks);

        if (empty($missingChunks)) {
            $finalPath = $this->assembleChunks($tempDirectory, $filename, $totalChunks);
            $fullDiskPath = Storage::path($finalPath);

            $record = FileStorage::create([
                'user_id' => $user->id,
                'folder_id' => $folderId,
                'original_name' => $filename,
                'file_path' => $finalPath,
                'mime_type' => Storage::mimeType($finalPath) ?? 'application/octet-stream',
                'file_size' => Storage::size($finalPath),
                'file_hash' => hash_file('sha256', $fullDiskPath),
                'storage_node' => config('app.storage_node', 'node-1'),
            ]);

            Storage::deleteDirectory($tempDirectory);

            return [
                'status' => 'completed',
                'message' => 'Upload berhasil diselesaikan.',
                'progress' => 100.0,
                'received' => $totalChunks,
                'total' => $totalChunks,
                'missingChunks' => [],
                'file' => $record,
            ];
        }

        return [
            'status' => 'uploading',
            'message' => "Chunk {$chunkIndex} berhasil diterima.",
            'progress' => round(($received / $totalChunks) * 100, 2),
            'received' => $received,
            'total' => $totalChunks,
            'missingChunks' => $missingChunks,
            'file' => null,
        ];
    }

    public function move($_, array $args): FileStorage
    {
        $user = auth('api')->user();
        $record = FileStorage::findOrFail($args['id']);

        if ($user->role === 'user' && $record->user_id !== $user->id) {
            throw new Error('Anda tidak memiliki akses untuk memindahkan file ini.');
        }

        $folderId = $args['folderId'] ?? null;

        if ($folderId) {
            $folder = Folder::findOrFail($folderId);
            if ($user->role === 'user' && $folder->user_id !== $user->id) {
                throw new Error('Anda tidak memiliki akses ke folder tujuan.');
            }
        }

        $record->update(['folder_id' => $folderId]);

        return $record;
    }

    public function delete($_, array $args): bool
    {
        $user = auth('api')->user();

        if (! in_array($user->role, ['super_admin', 'admin'], true)) {
            throw new Error('Anda tidak memiliki akses untuk menghapus file ini.');
        }

        $record = FileStorage::findOrFail($args['id']);

        if (Storage::exists($record->file_path)) {
            Storage::delete($record->file_path);
        }

        $record->delete();

        return true;
    }

    private function assembleChunks(string $tempDir, string $originalFilename, int $totalChunks): string
    {
        $safeName = Str::uuid().'_'.preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalFilename);
        $destinationRelativePath = "uploads/{$safeName}";
        $destinationFullPath = Storage::path($destinationRelativePath);

        Storage::makeDirectory('uploads');

        $outputStream = fopen($destinationFullPath, 'wb');

        clearstatcache(true);

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = Storage::path("{$tempDir}/part_{$i}");

            if (file_exists($chunkPath)) {
                $inputStream = fopen($chunkPath, 'rb');
                stream_copy_to_stream($inputStream, $outputStream);
                fclose($inputStream);
            }
        }

        fclose($outputStream);

        return $destinationRelativePath;
    }
}