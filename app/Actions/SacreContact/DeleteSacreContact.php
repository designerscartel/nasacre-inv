<?php

namespace App\Actions\SacreContact;

use App\Contracts\SacreContact\DeletesSacreContact;

class DeleteSacreContact implements DeletesSacreContact
{
    /**
     * Delete the given sacreContact.
     *
     * @param  mixed  $sacreContact
     * @return void
     */
    public function delete($sacreContact)
    {
        $sacreContact->delete();
    }
}