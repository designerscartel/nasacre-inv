<?php

namespace App\Actions\booking;

use App\Contracts\Booking\CreatesBookingRefInformation;
use App\Mail\BookingInvoiceForQueuing;
use App\Models\SacreBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;


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
            'delegate_one_diet' => ['max:255'],
            'delegate_two_name' => ['max:255'],
            'delegate_two_email' => ['max:255'],
            'delegate_two_diet' => ['max:255'],
            'virtual_one_name' => ['max:255'],
            'virtual_one_email' => ['max:255'],
            'virtual_two_name' => ['max:255'],
            'virtual_two_email' => ['max:255'],
            'confirmed' => ['max:255'],
        ]);

        if ($validator->fails()) {
            $errors = ['error' => $validator->errors()];
            return response()->json($errors, 400);
        }

        $date = \Carbon\Carbon::now();

        $bookingRequest = [
            'booking_id' => 1,
            'date' => $date->toDateTimeString(),
            'sacre_id' => $input['sacre_id'],
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'delegate_one_name' => $input['delegate_one_name'],
            'delegate_one_email' => $input['delegate_one_email'],
            'delegate_one_diet' => $input['delegate_one_diet'],
            'delegate_two_name' => $input['delegate_two_name'],
            'delegate_two_email' => $input['delegate_two_email'],
            'delegate_two_diet' => $input['delegate_two_diet'],
            'virtual_one_name' => $input['virtual_one_name'],
            'virtual_one_email' => $input['virtual_one_email'],
            'virtual_two_name' => $input['virtual_two_name'],
            'virtual_two_email' => $input['virtual_two_email'],
            'confirmed' => $input['confirmed'],
        ];

        if(!empty($input['confirmed'])) {
            $bookingData = SacreBooking::find($input['id']);
            $bookingData->fill($bookingRequest);
            $bookingData->save();
            $success = ['success' => [
                'status' => 'complete',
                'id' => $bookingData->id
            ]
            ];

            $email = new BookingInvoiceForQueuing($bookingData);
            Mail::to($bookingData->email)->send($email);

        } else {
            $bookingData = new SacreBooking();
            $bookingData = $bookingData->create($bookingRequest);
            $success = ['success' => [
                'status' => 'confirmed',
                'id' => $bookingData->id
            ]
            ];
        }


        $total = 0;
        $delegateCost = ($bookingData->sacre->member)? $bookingData->booking->subscribed : $bookingData->booking->none_subscribed;

        if(!empty($bookingData->delegate_one_name)) {
            $total = $total + $delegateCost;
        }
        if(!empty($bookingData->delegate_two_name)) {
            $total = $total + $delegateCost;
        }

        if(!empty($bookingData->virtual_one_name)) {
            $total = $total + $bookingData->booking->additional;
        }

        $success['success']['cost'] = $total;

        return response()->json($success);

    }
}
