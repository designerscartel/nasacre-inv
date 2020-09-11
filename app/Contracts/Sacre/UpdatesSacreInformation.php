<?php

namespace App\Contracts\Sacre;

interface UpdatesSacreInformation
{
    /**
     * Validate and update the given sacre's information.
     *
     * @param  mixed  $sacre
     * @param  array  $input
     * @return void
     */
    public function update($sacre, array $input);
}