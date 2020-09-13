<?php

namespace App\Contracts\SacreContact;

interface AddsSacreContact
{
    /**
     * Validate and update the given sacre's contact information.
     *
     * @param mixed $sacre
     * @param array  $input
     * @return void
     */
    public function add($sacre, array $input);
}