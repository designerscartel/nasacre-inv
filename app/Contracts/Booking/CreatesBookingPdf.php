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
    public function create(object $bookingData);

    /**
     * @param object $booking
     * @param object $sacre
     * @return mixed
     */
    public function output(object $bookingData);

    /**
     * @param object $booking
     * @param object $sacre
     * @return mixed
     */
    public function download(object $bookingData);

    /**
     * @param object $booking
     * @param object $sacre
     * @return mixed
     */
    public function inline(object $bookingData);
}