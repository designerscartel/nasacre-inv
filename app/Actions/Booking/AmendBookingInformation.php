<?php

namespace App\Actions\Booking;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Booking\AmendsBookingInformation;

class AmendBookingInformation implements AmendsBookingInformation
{
    /**
     * Validate and update the given campaign's information.
     *
     * @param mixed $sacreBooking
     * @param array $input
     * @return void
     */
    public function update($sacreBooking, array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateSacreBookingInformation');

        $sacreBooking->fill([
            'po_number' => $input['po_number'],
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'address' => $input['address'],
            'delegate_one_name' => $input['delegate_one_name'],
            'delegate_one_email' => $input['delegate_one_email'],
            'delegate_one_diet' => $input['delegate_one_diet'],
            'delegate_one_free' => ($input['delegate_one_free']) ? 1 :0,
            'delegate_two_name' => $input['delegate_two_name'],
            'delegate_two_email' => $input['delegate_two_email'],
            'delegate_two_free' => $input['delegate_two_free'],
            'delegate_two_diet' => $input['delegate_two_diet'],
            'virtual_one_name' => $input['virtual_one_name'],
            'virtual_one_email' => $input['virtual_one_email'],
            'virtual_one_free' => $input['virtual_one_free'],
            'virtual_two_name' => $input['virtual_two_name'],
            'virtual_two_email' => $input['virtual_two_email'],
            'virtual_two_free' => $input['virtual_two_free'],
            'confirmed' => $input['confirmed']
        ])->save();
    }
}
