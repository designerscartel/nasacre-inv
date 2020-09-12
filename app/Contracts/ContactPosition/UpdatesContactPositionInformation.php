<?php

namespace App\Contracts\ContactPosition;

interface UpdatesContactPositionInformation
{
    /**
     * Validate and update the given contact position's information.
     *
     * @param  mixed  $contactPosition
     * @param  array  $input
     * @return void
     */
    public function update($contactPosition, array $input);
}