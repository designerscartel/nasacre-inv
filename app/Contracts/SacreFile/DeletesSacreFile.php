<?php

namespace App\Contracts\SacreFile;


interface DeletesSacreFile
{
    /**
     * Delete the given scare file.
     *
     * @param  mixed  $sacreFile
     * @return void
     */
    public function delete($sacreFile);
}