<?php

namespace App\Contracts\Invoice;

interface SendsInvoiceInformation
{
    /**
     * Validate and sends the given Invoice's information.
     *
     * @param  mixed  $Invoice
     * @param  array  $input
     * @return void
     */
    public function send($Invoice, array $input);
}