<?php

namespace App\Actions\Booking;

use App\Models\BookingMemberSub;
use App\Models\BookingSub;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Booking\UpdatesBookingInformation;

class UpdateBookingInformation implements UpdatesBookingInformation
{
    /**
     * Validate and update the given campaign's information.
     *
     * @param  mixed  $booking
     * @param  array  $input
     * @return void
     */
    public function update($booking, array $input)
    {

        Validator::make($input, [
            'email' => ['required', 'string', 'max:255'],
            'subscribed' => ['required', 'string', 'max:255'],
            'none_subscribed' => ['required', 'string', 'max:255'],
            'additional_amount' => ['required', 'string', 'max:255'],
            'date' => ['required', 'string', 'max:255'],
            'from' => ['required', 'string', 'max:255'],
            'message' => ['required'],
            'venue' => ['required'],
        ])->validateWithBag('updateBookingInformation');

        $date = \Carbon\Carbon::parse($input['date']);

        $booking->fill([
            'email' => $input['email'],
            'subscribed' => $input['subscribed'],
            'none_subscribed' => $input['none_subscribed'],
            'additional_amount' => $input['additional_amount'],
            'date' => $date->toDateTimeString(),
            'year' => $date->format('Y'),
            'from' => $input['from'],
            'message' => $input['message'],
            'venue' => $input['venue'],
            'new' => $input['new'],
        ])->save();

        // clear subs
        $booking->subs()->delete();
        foreach ($input['subs'] as $key  => $sub ) {
            $subObject = new BookingSub(['sub' => $sub['sub']]);
            $booking->subs()->save($subObject);
        }

        // clear member subs
        $booking->memberSubs()->delete();
        foreach ($input['memberSubs'] as $key  => $sub ) {
            $subObject = new BookingMemberSub(['sub' => $sub['sub']]);
            $booking->memberSubs()->save($subObject);
        }


    }
}
