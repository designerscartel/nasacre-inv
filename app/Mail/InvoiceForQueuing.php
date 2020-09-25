<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Sacre;
use App\Models\SacreContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceForQueuing
 * @package App\Mail
 */
class InvoiceForQueuing extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Invoice
     */
    protected $invoice;

    /**
     * @var Sacre
     */
    protected $sacre;

    /**
     * @var SacreContact
     */
    protected $contact;

    /**
     * @var
     */
    protected $pdf;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, Sacre $sacre, SacreContact $contact, $pdf)
    {
        //
        $this->invoice = $invoice;
        $this->sacre = $sacre;
        $this->contact = $contact;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('mail@example.com', 'Mailtrap')
            ->subject('NASACRE Membership Invoice ' . $this->sacre->title . ' ' . $this->invoice->year)
            ->with([
                'invoice' => $this->invoice,
                'sacre' => $this->sacre,
                'contact' => $this->contact,
            ])
            ->attachData($this->pdf, $this->invoice->year . '-' . $this->sacre->short_code . '.pdf', [
                'mime' => 'application/pdf',
            ])
            ->markdown('mails.invoice-email');
    }
}
