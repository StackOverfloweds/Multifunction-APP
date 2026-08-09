<?php

namespace App\Http\Controllers;

use App\Models\FileStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
    /**
     * Show the list of file
     */
    public function index (Request $request) 
    {
        $user = $request->user();
        $query = FileStorage :: query()->with('user');

        // if the user can see the collection self
        if ($user->role === 'user') {
            $query -> where('user_id', $user->id);
        }
        // filter search name file or type doc
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'LIKE', "%{$search}%")
                  ->orWhere('mime_type', 'LIKE', "%{$search}%");
            });
        }

        $files = $query->latest()->paginate(15);
        return view('storage.index', compact('files'));
    }

    /**
     * Delete a stored file (physical file + database record)
     */
    public function destroy (Request $request, string $id) :\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
 
        if (! in_array($user->role, ['super_admin', 'admin'], true)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus file ini.');
        }
 
        $record = FileStorage::findOrFail($id);
 
        if (Storage::exists($record->file_path)) {
            Storage::delete($record->file_path);
        }
 
        $record->delete();
 
        return redirect()->route('storage.index')->with('success', 'File berhasil dihapus.');
    }

    /**
     * process the accetp and merge the chunked file
     */
    public function storeChunk (Request $request) :JsonResponse 
    {
        $validated = $request ->validate([
            'file' => ['required', 'file'],
            'upload_id' => ['required', 'string', 'alpha_dash'],
            'chunk_index' => ['required', 'integer', 'min:0', 'lt:total_chunks'],
            'total_chunks' => ['required', 'integer', 'min:1'],
            'filename' => ['required', 'string', 'max:255'],
        ]);
 
        $uploadId = $validated['upload_id'];
        $chunkIndex = $validated['chunk_index'];
        $totalChunks = $validated['total_chunks'];
        $filename = $validated['filename'];
 
        // directory isolation for a while
        $tempDirectory = "storage/temp/{$uploadId}";
 
        // save the chunked file 
        $request->file('file')->storeAs($tempDirectory, "part_{$chunkIndex}");
 
        // Bersihkan stat/realpath cache PHP sebelum mengecek isi direktori.
        clearstatcache(true);
 
        // Verifikasi keberadaan setiap chunk secara eksplisit (part_0 s/d part_N-1)
        $missingChunks = [];
        for ($i = 0; $i < $totalChunks; $i++) {
            if (!Storage::exists("{$tempDirectory}/part_{$i}")) {
                $missingChunks[] = $i;
            }
        }
        $uploadedChunked = $totalChunks - count($missingChunks);
 
        // if the chunked file is completely fix, then add all
        if (empty($missingChunks)) 
        {
            $finalPath = $this->assembleChunks($tempDirectory, $filename, $totalChunks);
 
            $fullDiskPath = Storage::path($finalPath);
 
            $record = FileStorage::create([
                'user_id' => $request->user()->id,
                'original_name' => $filename,
                'file_path' => $finalPath,
                'mime_type' => Storage::mimeType($finalPath) ?? 'application/octet-stream',
                'file_size' => Storage::size($finalPath),
                'file_hash' => hash_file('sha256', $fullDiskPath),
                'storage_node' => config('app.storage_node', 'node-1'),
            ]);
 
            Storage::deleteDirectory($tempDirectory);
 
            return response()->json([
                'status' => 'completed',
                'message' => 'Upload berhasil diselesaikan.',
                'data' => $record,
            ], 201);
        }
 
        return response()->json([
            'status' => 'uploading',
            'progress' => round(($uploadedChunked / $totalChunks) * 100, 2),
            'received' => $uploadedChunked,
            'total' => $totalChunks,
            'missing_chunks' => $missingChunks,
            'message' => "Chunk {$chunkIndex} berhasil diterima.",
        ]);
        }

        /**
         * Download a Stored File
         * 
         */
        public function download (Request $request, string $id) :\Symfony\Component\HttpFoundation\StreamedResponse
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
        private function assembleChunks (string $tempDir, string $originalFilename, int $totalChunks) :string
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