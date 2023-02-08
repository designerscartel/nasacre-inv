<?php

namespace App\Actions\Booking;

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
        $data = [
            'bookingData' => $bookingData,
            'date' => \Carbon\Carbon::parse($bookingData->booking->date)
        ];

        $pdf = \PDF::loadView('pdfs.booking', $data);

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
