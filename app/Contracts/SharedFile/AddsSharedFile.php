<?php

namespace App\Contracts\SharedFile;

interface AddsSharedFile
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