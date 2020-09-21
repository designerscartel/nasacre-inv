<?php

namespace App\Contracts\Invoice;

interface UpdatesInvoiceInformation
{
    /**
     * Validate and update the given invoice's information.
     *
     * @param  mixed  $invoice
     * @param  array  $input
     * @return void
     */
    public function update($invoice, array $input);
}