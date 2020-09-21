<?php

namespace App\Contracts\Invoice;

interface CreatesInvoiceInformation
{
    /**
     * Validate and update the given invoice's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input);
}