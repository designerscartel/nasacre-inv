<?php

namespace App\Contracts\SacreInvoice;

interface UpdatesSacreInvoiceInformation
{
    /**
     * Validate and update the given invoice's information.
     *
     * @param  mixed  $sacreInvoice
     * @param  array  $input
     * @return void
     */
    public function update($sacreInvoice, array $input);
}