<?php

namespace App\Contracts\Booking;

interface UpdatesBookingInformation
{
    /**
     * Validate and update the given booking's information.
     *
     * @param  mixed  $booking
     * @param  array  $input
     * @return void
     */
    public function update($booking, array $input);
}