<?php

namespace App\Contracts\ContactPosition;

interface CreatesContactPositionInformation
{
    /**
     * Validate and update the given region's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input);
}