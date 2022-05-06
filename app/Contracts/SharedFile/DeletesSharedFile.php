<?php

namespace App\Contracts\SharedFile;


interface DeletesSharedFile
{
    /**
     * Delete the given scare file.
     *
     * @param  mixed  $sacreFile
     * @return void
     */
    public function delete($sacreFile);
}