<?php

namespace App\Actions\SacreFile;

use App\Contracts\SacreFile\DeletesSacreFile;

class DeleteSacreFile implements DeletesSacreFile
{
    /**
     * Delete the given sacreFile.
     *
     * @param  mixed  $sacreFile
     * @return void
     */
    public function delete($sacreFile)
    {
        $sacreFile->delete();
    }
}