<?php

namespace App\Actions\Invoice;

use App\Contracts\Invoice\DeletesInvoice;

class DeleteInvoice implements DeletesInvoice
{
    /**
     * Delete the given invoice.
     *
     * @param  mixed  $invoice
     * @return void
     */
    public function delete($invoice)
    {
        $invoice->delete();
    }
}