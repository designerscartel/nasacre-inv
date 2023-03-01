<?php

namespace App\Actions\Booking;

use App\Contracts\Booking\DeletesBookingInformation;

class DeleteBookingInformation implements DeletesBookingInformation
{
    /**
     * Delete the given booking.
     *
     * @param  mixed  $scareBooking
     * @return void
     */
    public function delete($scareBooking)
    {
        $scareBooking->delete();
    }
}