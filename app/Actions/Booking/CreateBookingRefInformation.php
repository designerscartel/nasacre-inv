<?php

namespace App\Actions\booking;

use App\Contracts\Booking\CreatesBookingRefInformation;
use App\Models\SacreBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class CreateBookingRefInformation implements CreatesBookingRefInformation
{
    /**
     * Validate and Create the given campaign's information.
     *
     * @param Request $input
     * @return void
     */
    public function create(Request $input)
    {

        $validator = Validator::make($input->all(), [
            'sacre_id' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'delegate_one_name' => ['required', 'string', 'max:255'],
            'delegate_one_email' => ['required', 'string', 'max:255'],
            'delegate_two_name' => ['string', 'max:255'],
            'delegate_two_email' => ['string', 'max:255'],
            'virtual_one_name' => ['string', 'max:255'],
            'virtual_one_email' => ['string', 'max:255'],
            'virtual_two_name' => ['string', 'max:255'],
            'virtual_two_email' => ['string', 'max:255'],
        ]);

        if ($validator->fails()) {
            dd($validator);
        }

        $booking = new SacreBooking();
        $date = \Carbon\Carbon::now();

        return $booking->create([
            'booking_id' => 1,
            'date' => $date->toDateTimeString(),
            'sacre_id' => $input['sacre_id'],
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'delegate_one_name' => $input['delegate_one_name'],
            'delegate_one_email' => $input['delegate_one_email'],
            'delegate_two_name' => $input['delegate_two_name'],
            'delegate_two_email' => $input['delegate_two_email'],
            'virtual_one_name' => $input['virtual_one_name'],
            'virtual_one_email' => $input['virtual_one_email'],
            'virtual_two_name' => $input['virtual_two_name'],
            'virtual_two_email' => $input['virtual_two_email'],
        ]);

    }
}
