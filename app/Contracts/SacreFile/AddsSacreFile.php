<?php

namespace App\Contracts\SacreFile;

interface AddsSacreFile
{
    /**
     * Validate and update the given sacre's file information.
     *
     * @param mixed $sacre
     * @param array  $input
     * @return void
     */
    public function add($sacre, array $input);
}