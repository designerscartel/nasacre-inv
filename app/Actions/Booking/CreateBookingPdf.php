<?php

namespace App\Actions\Booking;

use App\Models\BookingDelegate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Booking\CreatesBookingPdf;

/**
 * Class CreateBookingPdf
 * @package App\Actions\booking
 */
class CreateBookingPdf implements CreatesBookingPdf
{
    /**
     * Create the given booking's pdf.
     *
     * @param object $bookingData
     * @return void
     */
    public function create(object $bookingData)
    {
        //

        $delegates = BookingDelegate::where('sacre_booking_id', $bookingData->id)->get();

        $data = [
            'bookingData' => $bookingData,
            'bookingDataDate' => \Carbon\Carbon::parse($bookingData->booking->date),
            'date' => \Carbon\Carbon::parse($bookingData->date),
            'delegates' => $delegates
        ];

        $pdf = \PDF::loadView('pdfs.booking-new', $data);

        return $pdf;
    }

    /**
     * @param object $bookingData
     * @return mixed
     */
    public function output(object $bookingData)
    {
        //
        $pdf = $this->create($bookingData);
        return $pdf->output();
    }

    /**
     * @param object $bookingData
     * @return mixed
     */
    public function download(object $bookingData)
    {
        //
        $pdf = $this->create($bookingData);
        return $pdf->download($bookingData->name . '.pdf');
    }

    /**
     * @param object $bookingData
     * @return mixed
     */
    public function inline(object $bookingData)
    {
        //
        $pdf = $this->create($bookingData);
        return $pdf->inline($bookingData->name.'.pdf');
    }
}
