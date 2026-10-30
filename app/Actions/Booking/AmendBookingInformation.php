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
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'po_number' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'confirmed' => ['required', 'boolean'],
        ])->validateWithBag('updateSacreBookingInformation');

        $sacreBooking->fill([
            'po_number' => $input['po_number'] ?? null,
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'address' => $input['address'] ?? null,
            'delegate_one_name' => $input['delegate_one_name'] ?? null,
            'delegate_one_email' => $input['delegate_one_email'] ?? null,
            'delegate_one_diet' => $input['delegate_one_diet'] ?? null,
            'delegate_one_free' => !empty($input['delegate_one_free']),
            'delegate_two_name' => $input['delegate_two_name'] ?? null,
            'delegate_two_email' => $input['delegate_two_email'] ?? null,
            'delegate_two_free' => !empty($input['delegate_two_free']),
            'delegate_two_diet' => $input['delegate_two_diet'] ?? null,
            'virtual_one_name' => $input['virtual_one_name'] ?? null,
            'virtual_one_email' => $input['virtual_one_email'] ?? null,
            'virtual_one_free' => !empty($input['virtual_one_free']),
            'virtual_two_name' => $input['virtual_two_name'] ?? null,
            'virtual_two_email' => $input['virtual_two_email'] ?? null,
            'virtual_two_free' => !empty($input['virtual_two_free']),
            'confirmed' => (bool) $input['confirmed'],
        ])->save();
    }
}
