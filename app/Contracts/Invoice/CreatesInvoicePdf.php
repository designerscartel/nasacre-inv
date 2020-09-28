<?php

namespace App\Contracts\Invoice;

/**
 * Interface CreatesInvoicePdf
 * @package App\Contracts\Invoice
 */
interface CreatesInvoicePdf
{
    /**
     * Validate and update the given invoice's pdf.
     *
     * @param  object  $invoice
     * @param  object  $sacre
     * @return void
     */
    public function create(object $invoice, object $sacre);

    /**
     * @param object $invoice
     * @param object $sacre
     * @return mixed
     */
    public function output(object $invoice, object $sacre);

    /**
     * @param object $invoice
     * @param object $sacre
     * @return mixed
     */
    public function download(object $invoice, object $sacre);

    /**
     * @param object $invoice
     * @param object $sacre
     * @return mixed
     */
    public function inline(object $invoice, object $sacre);
}