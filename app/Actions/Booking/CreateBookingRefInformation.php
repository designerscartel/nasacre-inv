<?php

namespace App\Actions\Booking;

use App\Contracts\Booking\CreatesBookingRefInformation;
use App\Models\Booking;
use App\Models\BookingDelegate;
use App\Models\Sacre;
use App\Models\SacreBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


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
            'sacre_id' => ['required', 'integer', 'exists:sacres,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'offer' => ['nullable', 'boolean'],
            'delegate' => ['required', 'array', 'min:1'],
            'delegate.*.name' => ['nullable', 'required_with:delegate.*.email', 'string', 'max:255'],
            'delegate.*.email' => ['nullable', 'required_with:delegate.*.name', 'email', 'max:255'],
            'delegate.*.diet' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            $errors = ['error' => $validator->errors()];
            return response()->json($errors, 400);
        }

        $bookingReference = Booking::where('active', true)->first();
        $sacreReference = Sacre::find($input['sacre_id']);

        if (! $bookingReference) {
            return response()->json(['error' => ['status' => 'No booking is currently open']], 422);
        }

        $delegates = collect($input['delegate'])->filter(function ($delegate) {
            return !empty($delegate['name']) && !empty($delegate['email']);
        })->values();

        $total = $bookingReference->priceForDelegateCount($delegates->count(), (bool) $sacreReference->member);

        if ($total === null) {
            return response()->json(['error' => ['delegate' => ['No price is configured for this number of delegates.']]], 422);
        }

        $offer = !empty($input['offer']);
        if ($offer) {
            $total += (float) $bookingReference->additional_amount;
        }

        $bookingData = DB::transaction(function () use ($input, $bookingReference, $delegates, $offer) {
            $bookingData = SacreBooking::create([
                'booking_id' => $bookingReference->id,
                'date' => now(),
                'sacre_id' => $input['sacre_id'],
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'confirmed' => false,
                'offer' => $offer,
            ]);

            foreach ($delegates as $delegate) {
                $bookingData->delegates()->create([
                    'name' => $delegate['name'],
                    'email' => $delegate['email'],
                    'diet' => $delegate['diet'] ?? null,
                    'sacre_id' => $input['sacre_id'],
                    'booking_id' => $bookingReference->id,
                ]);
            }

            return $bookingData;
        });

        $success = ['success' => [
            'status' => 'pending',
            'id' => $bookingData->id
        ]];

        $success['success']['cost'] = $total;

        return response()->json($success);

    }
}
