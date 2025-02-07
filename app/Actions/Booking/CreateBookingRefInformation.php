<?php

namespace App\Actions\booking;

use App\Contracts\Booking\CreatesBookingPdf;
use App\Contracts\Booking\CreatesBookingRefInformation;
use App\Mail\BookingInvoiceForQueuing;
use App\Models\Booking;
use App\Models\BookingDelegate;
use App\Models\Sacre;
use App\Models\SacreBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
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
            'confirmed' => ['max:255'],
        ]);

        if ($validator->fails()) {
            $errors = ['error' => $validator->errors()];
            return response()->json($errors, 400);
        }

        $bookingReference = Booking::find(3);
        $sacreReference = Sacre::find($input['sacre_id']);

        if (empty($sacreReference)) {
            $error = ['error' => ['status' => 'SACRE Needed']];
            return response()->json($error, 400);
        }


        $date = \Carbon\Carbon::now();

        $bookingRequest = [
            'booking_id' => $bookingReference->id,
            'date' => $date->toDateTimeString(),
            'sacre_id' => $input['sacre_id'],
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'confirmed' => $input['confirmed']
        ];

        $delegates = [];

        if (!empty($input['confirmed'])) {
            $bookingData = SacreBooking::find($input['id']);
            $bookingData->fill($bookingRequest);
            $bookingData->save();
            $success = ['success' => [
                'status' => 'complete',
                'id' => $bookingData->id
            ]
            ];

            $delegates = BookingDelegate::where('sacre_booking_id', $bookingData->id)->get();

            // create invoice
            $pdf = app(CreatesBookingPdf::class)->output($bookingData);

            $email = new BookingInvoiceForQueuing($bookingData, $pdf);
            Mail::to('matt@papercode.co.uk')->send($email);
            // Mail::to('admin@nasacre.org.uk')->send($email);



        } else {
            $bookingData = new SacreBooking();

            $bookingRequest['offer'] = ($input['offer'] ?? 0);

            $bookingData = $bookingData->create($bookingRequest);

            foreach ($input['delegate'] as $key => $delegate) {
                if (!empty($delegate['name']) && !empty($delegate['email'])) {
                    $delegateObj = new BookingDelegate();
                    $delegateObj->name = $delegate['name'];
                    $delegateObj->email = $delegate['email'];
                    $delegateObj->diet = $delegate['diet'];
                    $delegateObj->sacre_id = $input['sacre_id'];
                    $delegateObj->booking_id = $bookingReference->id;
                    $delegateObj->sacre_booking_id = $bookingData->id;
                    $delegateObj->save();
                    $delegates[] = $delegateObj;
                }
            }

            $success = ['success' => [
                'status' => 'confirmed',
                'id' => $bookingData->id
            ]
            ];
        }

        $total = 0;
        $costArray = [];
        $subsCosts = $bookingReference->subs;

        if ($sacreReference->member == 1) {
            $subsCosts = $bookingReference->memberSubs;
        }

        foreach ($subsCosts as $key => $cost) {
            $costArray[] = $cost->sub;
        }

        foreach ($delegates as $key => $delegate) {
            $total = $costArray[$key];
        }

        if($bookingData->offer != 0) {
            $total = $total + $bookingReference->additional_amount;
        }

        $success['success']['cost'] = $total;

        return response()->json($success);

    }
}
