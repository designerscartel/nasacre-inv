<?php

namespace App\Actions\booking;

use App\Models\Booking;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Booking\CreatesBookingInformation;

class CreateBookingInformation implements CreatesBookingInformation
{
    /**
     * Validate and Create the given campaign's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'email' => ['required', 'string', 'max:255'],
            'subscribed' => ['required', 'string', 'max:255'],
            'none_subscribed' => ['required', 'string', 'max:255'],
            'additional' => ['required', 'string', 'max:255'],
            'date' => ['required', 'string', 'max:255'],
            'from' => ['required', 'string', 'max:255'],
            'message' => ['required'],
            'venue' => ['required'],
        ])->validateWithBag('createBookingInformation');

        $booking = new Booking();
        $date = \Carbon\Carbon::parse($input['date']);

        return $booking->create([
            'email' => $input['email'],
            'subscribed' => $input['subscribed'],
            'none_subscribed' => $input['none_subscribed'],
            'additional' => $input['additional'],
            'date' => $date->toDateTimeString(),
            'year' => $date->format('Y'),
            'from' => $input['from'],
            'message' => $input['message'],
            'venue' => $input['venue'],
        ]);

    }
}
