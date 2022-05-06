<?php

namespace App\Actions\SharedFile;

use App\Contracts\SharedFile\DeletesSharedFile;

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
        $SharedFile->delete();
    }
}