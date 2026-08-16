<?php

namespace App\GraphQL\Mutations;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileMutator
{
    public function upload($_, array $args): File
    {
        /** @var UploadedFile $uploaded */
        $uploaded = $args['file'];

        $user = auth('api')->user();

        $path = $uploaded->store("uploads/{$user->id}", 'public');

        return File::create([
            'user_id' => $user->id,
            'name' => $uploaded->getClientOriginalName(),
            'path' => $path,
            'size' => $uploaded->getSize(),
            'mime_type' => $uploaded->getClientMimeType(),
        ]);
    }

    public function delete($_, array $args): bool
    {
        $user = auth('api')->user();

        $file = File::query()
            ->where('id', '=', $args['id'])
            ->where('user_id', '=', $user->id)
            ->firstOrFail();

        Storage::disk('public')->delete($file->path);

        $file->delete($file);

        return true;
    }
}