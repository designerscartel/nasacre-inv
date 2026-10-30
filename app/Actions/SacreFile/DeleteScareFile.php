<?php

namespace App\Actions\SacreFile;

use App\Contracts\SacreFile\DeletesSacreFile;
use Illuminate\Support\Facades\Storage;

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
        Storage::delete('files/'.$sacreFile->filename);
        $sacreFile->delete();
    }
}
