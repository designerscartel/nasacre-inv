<?php

namespace App\Actions\booking;

use App\Models\Booking;
use App\Models\BookingSub;
use App\Models\BookingMemberSub;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Booking\CreatesBookingInformation;
use Illuminate\Support\Facades\DB;

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
        ])->validateWithBag('createBookingInformation');

        $date = \Carbon\Carbon::parse($input['date']);

        return DB::transaction(function () use ($input, $date) {
            $active = !empty($input['active']);

            if ($active) {
                Booking::query()->update(['active' => false]);
            }

            $bookingObject = Booking::create([
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
            ]);

            foreach ($input['subs'] as $sub) {
                $bookingObject->subs()->save(new BookingSub(['sub' => $sub['sub']]));
            }

            foreach ($input['memberSubs'] as $sub) {
                $bookingObject->memberSubs()->save(new BookingMemberSub(['sub' => $sub['sub']]));
            }

            return $bookingObject;
        });

    }
}
