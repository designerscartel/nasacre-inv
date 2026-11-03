<?php

namespace App\Contracts\Invoice;

interface RetriesFailedInvoiceDeliveries
{
    public function retry($invoice, array $input);
}
