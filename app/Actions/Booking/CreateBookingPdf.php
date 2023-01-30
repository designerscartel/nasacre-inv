<?php

namespace App\Actions\booking;

use App\Models\Booking;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\booking\CreatesBookingPdf;

/**
 * Class CreatebookingPdf
 * @package App\Actions\booking
 */
class CreateBookingPdf implements CreatesBookingPdf
{
    /**
     * Create the given booking's pdf.
     *
     * @param object $booking
     * @param object $sacre
     * @param object $contact
     * @return void
     */
    public function create(object $booking, object $sacre)
    {
        //
        $data = [
            'booking' => $booking,
            'sacre' => $sacre,
            'date' => \Carbon\Carbon::parse($booking->date)
        ];

        $pdf = \PDF::loadView('pdfs.booking', $data);

        return $pdf;
    }

    /**
     * @param object $booking
     * @param object $sacre
     * @return mixed
     */
    public function output(object $booking, object $sacre)
    {
        //
        $pdf = $this->create($booking, $sacre);
        return $pdf->output();
    }

    /**
     * @param object $booking
     * @param object $sacre
     * @return mixed
     */
    public function download(object $booking, object $sacre)
    {
        //
        $pdf = $this->create($booking, $sacre);
        return $pdf->download($booking->year . '-' . $sacre->short_code . '.pdf');
    }

    /**
     * @param object $booking
     * @param object $sacre
     * @return mixed
     */
    public function inline(object $booking, object $sacre)
    {
        //
        $pdf = $this->create($booking, $sacre);
        return $pdf->inline($booking->year . '-' . $sacre->short_code . '.pdf');
    }
}
