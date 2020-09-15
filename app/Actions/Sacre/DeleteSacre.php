<?php

namespace App\Actions\Sacre;

use App\Contracts\Sacre\DeletesSacre;

class DeleteSacre implements DeletesSacre
{
    /**
     * Delete the given sacre.
     *
     * @param  mixed  $sacre
     * @return void
     */
    public function delete($sacre)
    {
        $sacre->delete();
    }
}