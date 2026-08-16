<?php

namespace App\Http\Controllers;

use App\Models\FileStorage;
use App\Models\Folder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    /**
     * Download a Stored File
     * 
     */
    public function download(Request $request, string $id): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = $request->user();
        $record = FileStorage::findOrFail($id);

        // role user only can download
        if ($user->role === 'user' && $record->user_id !== $user->id) {
            abort(403, 'You dont have access to this file');
        }

        if (! Storage::exists($record->file_path)) {
            abort(404, 'File Not Found');
        }
        return Storage::download($record->file_path, $record->original_name);
    }

    /**
     * function private to add some chunked file that can be one
     */
    private function assembleChunks(string $tempDir, string $originalFilename, int $totalChunks): string
    {
        $safeName = \Illuminate\Support\Str::uuid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalFilename);
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
