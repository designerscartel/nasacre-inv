<?php

namespace App\Contracts\SacreContact;


interface DeletesSacreContact
{
    /**
     * Delete the given scare contact.
     *
     * @param  mixed  $sacreContact
     * @return void
     */
    public function delete($sacreContact);
}