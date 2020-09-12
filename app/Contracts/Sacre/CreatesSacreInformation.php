<?php

namespace App\Contracts\Sacre;

interface CreatesSacreInformation
{
    /**
     * Validate and update the given sacre's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input);
}