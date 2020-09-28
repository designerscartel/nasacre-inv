<?php

namespace App\Contracts\SacreContact;

interface UpdatesSacreContact
{
    /**
     * Validate and update the given sacre's contact information.
     *
     * @param mixed $sacre
     * @param array  $input
     * @return void
     */
    public function update($sacreContact, $sacre, array $input);
}