<?php

namespace App\Contracts\Invoice;

interface CreatesInvoicePdf
{
    /**
     * Validate and update the given invoice's pdf.
     *
     * @param  object  $invoice
     * @param  object  $sacre
     * @param  object  $contact
     * @return void
     */
    public function create(object $invoice, object $sacre, object $contact);
}