<?php

namespace App\Contracts\Booking;


interface DeletesBooking
{
    /**
     * Delete the given scare.
     *
     * @param  mixed  $booking
     * @return void
     */
    public function delete($booking);
}