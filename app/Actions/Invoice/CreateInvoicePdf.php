<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Invoice\CreatesInvoicePdf;

/**
 * Class CreateInvoicePdf
 * @package App\Actions\Invoice
 */
class CreateInvoicePdf implements CreatesInvoicePdf
{
    /**
     * Create the given invoice's pdf.
     *
     * @param object $invoice
     * @param object $sacre
     * @param object $contact
     * @return void
     */
    public function create(object $invoice, object $sacre, object $contact)
    {

        $data = [
            'invoice' => $invoice,
            'sacre' => $sacre,
            'contact' => $contact,
            'date' => \Carbon\Carbon::parse($invoice->date)
        ];

        $pdf = \PDF::loadView('pdfs.invoice', $data);

        return $pdf->output();
    }
}
