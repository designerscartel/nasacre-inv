<?php

namespace App\Contracts\Region;

interface UpdatesRegionInformation
{
    /**
     * Validate and update the given region's information.
     *
     * @param  mixed  $region
     * @param  array  $input
     * @return void
     */
    public function update($region, array $input);
}