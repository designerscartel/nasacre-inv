<?php

namespace App\Actions\SharedFile;

use App\Contracts\SharedFile\DeletesSharedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DeleteSharedFile implements DeletesSharedFile
{
    /**
     * Delete the given SharedFile.
     *
     * @param  mixed  $SharedFile
     * @return void
     */
    public function delete($SharedFile)
    {
        $path = 'files/'.$SharedFile->filename;

        if (Storage::exists($path) && ! Storage::delete($path)) {
            throw ValidationException::withMessages([
                'file' => 'The stored file could not be deleted.',
            ])->errorBag('deleteFileForm');
        }

        $SharedFile->delete();
    }
}
