<?php

namespace App\Contracts\SacreFile;

interface UpdatesSacreFile
{
    /**
     * Validate and update the given sacre's file information.
     *
     * @param mixed $sacre
     * @param array  $input
     * @return void
     */
    public function update($sacreFile, $sacre, array $input);
}