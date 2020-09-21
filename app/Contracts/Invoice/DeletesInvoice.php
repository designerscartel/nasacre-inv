<?php

namespace App\Contracts\Invoice;


interface DeletesInvoice
{
    /**
     * Delete the given scare.
     *
     * @param  mixed  $invoice
     * @return void
     */
    public function delete($invoice);
}