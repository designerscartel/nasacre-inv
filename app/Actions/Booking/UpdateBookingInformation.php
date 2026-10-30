<?php

namespace App\Actions\Booking;

use App\Models\BookingMemberSub;
use App\Models\BookingSub;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Booking\UpdatesBookingInformation;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

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
            'email' => ['required', 'email', 'max:255'],
            'subscribed' => ['required', 'numeric', 'min:0'],
            'none_subscribed' => ['required', 'numeric', 'min:0'],
            'additional_amount' => ['nullable', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'from' => ['required', 'string', 'max:255'],
            'message' => ['required'],
            'venue' => ['required'],
            'new' => ['nullable', 'boolean'],
            'active' => ['nullable', 'boolean'],
            'subs' => ['required', 'array', 'min:1'],
            'subs.*.sub' => ['required', 'numeric', 'min:0'],
            'memberSubs' => ['required', 'array', 'min:1'],
            'memberSubs.*.sub' => ['required', 'numeric', 'min:0'],
        ])->validateWithBag('updateBookingInformation');

        $date = \Carbon\Carbon::parse($input['date']);

        DB::transaction(function () use ($booking, $input, $date) {
            $active = !empty($input['active']);

            if ($active) {
                Booking::where('id', '!=', $booking->getKey())->update(['active' => false]);
            }

            $booking->fill([
                'email' => $input['email'],
                'subscribed' => $input['subscribed'],
                'none_subscribed' => $input['none_subscribed'],
                'additional_amount' => $input['additional_amount'] ?? 0,
                'date' => $date->toDateTimeString(),
                'year' => $date->format('Y'),
                'from' => $input['from'],
                'message' => $input['message'],
                'venue' => $input['venue'],
                'new' => !empty($input['new']),
                'active' => $active,
            ])->save();

            $booking->subs()->delete();
            foreach ($input['subs'] as $sub) {
                $booking->subs()->save(new BookingSub(['sub' => $sub['sub']]));
            }

            $booking->memberSubs()->delete();
            foreach ($input['memberSubs'] as $sub) {
                $booking->memberSubs()->save(new BookingMemberSub(['sub' => $sub['sub']]));
            }
        });


    }
}
