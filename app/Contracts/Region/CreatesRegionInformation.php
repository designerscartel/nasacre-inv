<?php

namespace App\Contracts\Region;

interface CreatesRegionInformation
{
    /**
     * Validate and update the given region's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input);
}