<?php

namespace App\Contracts\Booking;

/**
 * Interface CreatesBookingPdf
 * @package App\Contracts\Booking
 */
interface CreatesBookingPdf
{
    /**
     * Validate and update the given booking's pdf.
     *
     * @param  object  $booking
     * @param  object  $sacre
     * @return void
     */
    public function create(object $booking, object $sacre);

    /**
     * @param object $booking
     * @param object $sacre
     * @return mixed
     */
    public function output(object $booking, object $sacre);

    /**
     * @param object $booking
     * @param object $sacre
     * @return mixed
     */
    public function download(object $booking, object $sacre);

    /**
     * @param object $booking
     * @param object $sacre
     * @return mixed
     */
    public function inline(object $booking, object $sacre);
}