<?php

namespace App\Contracts\Booking;

interface CreatesBookingInformation
{
    /**
     * Validate and update the given booking's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input);
}