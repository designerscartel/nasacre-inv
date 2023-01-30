<?php

namespace App\Contracts\Booking;

use Illuminate\Http\Request;

interface CreatesBookingRefInformation
{
    /**
     * Validate and update the given booking's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(Request $input);
}