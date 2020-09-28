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
    public function create(object $invoice, object $sacre)
    {
        //
        $data = [
            'invoice' => $invoice,
            'sacre' => $sacre,
            'date' => \Carbon\Carbon::parse($invoice->date)
        ];

        $pdf = \PDF::loadView('pdfs.invoice', $data);

        return $pdf;
    }

    /**
     * @param object $invoice
     * @param object $sacre
     * @return mixed
     */
    public function output(object $invoice, object $sacre)
    {
        //
        $pdf = $this->create($invoice, $sacre);
        return $pdf->output();
    }

    /**
     * @param object $invoice
     * @param object $sacre
     * @return mixed
     */
    public function download(object $invoice, object $sacre)
    {
        //
        $pdf = $this->create($invoice, $sacre);
        return $pdf->download($invoice->year . '-' . $sacre->short_code . '.pdf');
    }

    /**
     * @param object $invoice
     * @param object $sacre
     * @return mixed
     */
    public function inline(object $invoice, object $sacre)
    {
        //
        $pdf = $this->create($invoice, $sacre);
        return $pdf->inline($invoice->year . '-' . $sacre->short_code . '.pdf');
    }
}
