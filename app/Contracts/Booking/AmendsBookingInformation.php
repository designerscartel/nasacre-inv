<?php

namespace App\Contracts\Booking;

interface AmendsBookingInformation
{
    /**
     * Validate and update the given booking's information.
     *
     * @param  mixed  $sacreBooking
     * @param  array  $input
     * @return void
     */
    public function update($sacreBooking, array $input);
}