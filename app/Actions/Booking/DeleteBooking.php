<?php

namespace App\Actions\Booking;

use App\Contracts\Booking\DeletesBooking;

class DeleteBooking implements DeletesBooking
{
    /**
     * Delete the given booking.
     *
     * @param  mixed  $booking
     * @return void
     */
    public function delete($booking)
    {
        $booking->delete();
    }
}