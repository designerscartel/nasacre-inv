<?php

namespace App\Actions\SacreFile;

use App\Contracts\SacreFile\DeletesSacreFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DeleteScareFile implements DeletesSacreFile
{
    /**
     * Delete the given sacreFile.
     *
     * @param  mixed  $sacreFile
     * @return void
     */
    public function delete($sacreFile)
    {
        $path = 'files/'.$sacreFile->filename;

        if (Storage::exists($path) && ! Storage::delete($path)) {
            throw ValidationException::withMessages([
                'file' => 'The stored file could not be deleted.',
            ])->errorBag('deleteFileForm');
        }

        $sacreFile->delete();
    }
}
