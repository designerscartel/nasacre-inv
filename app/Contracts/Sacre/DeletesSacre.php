<?php

namespace App\Contracts\Sacre;


interface DeletesSacre
{
    /**
     * Delete the given scare.
     *
     * @param  mixed  $sacre
     * @return void
     */
    public function delete($sacre);
}