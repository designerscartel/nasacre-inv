<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Sacre;
use App\Models\BookingDelegate;

use App\Models\SacreBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BookingForQueuing
 * @package App\Mail
 */
class BookingInvoiceForQueuing extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var SacreBooking
     */
    protected $sacreBooking;

    /**
     * @var
     */
    protected $pdf;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SacreBooking $sacreBooking, $pdf)
    {
        //
        $this->sacreBooking = $sacreBooking;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $delegates = BookingDelegate::where('sacre_booking_id', $this->sacreBooking->id)->get();

        return $this->from('admin@nasacre.org.uk', 'NASACRE')
            ->subject('NASACRE Conference Booking')
            ->with([
                'sacreBooking' => $this->sacreBooking,
                'delegates' => $delegates
            ])
            ->markdown('mails.booking-invoice-new')
            ->attachData($this->pdf, 'Attendance at the NASACRE Conference and AGM.pdf', [
                'mime' => 'application/pdf',
            ]);;
    }
}
