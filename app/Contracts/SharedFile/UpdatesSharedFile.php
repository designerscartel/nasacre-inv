<?php

namespace App\Contracts\SharedFile;

interface UpdatesSharedFile
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