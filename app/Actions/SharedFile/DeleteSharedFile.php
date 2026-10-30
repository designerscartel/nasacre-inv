<?php

namespace App\Actions\SharedFile;

use App\Contracts\SharedFile\DeletesSharedFile;
use Illuminate\Support\Facades\Storage;

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
        Storage::delete('files/'.$SharedFile->filename);
        $SharedFile->delete();
    }
}
