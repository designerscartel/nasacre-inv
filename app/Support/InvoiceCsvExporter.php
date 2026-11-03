<?php

namespace App\Support;

use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceCsvExporter
{
    public function header(): array
    {
        return [
            'SACRE',
            'Invoice Number',
            'Amount',
            'Batch',
            'Sent Date',
            'Delivery Status',
        ];
    }

    public function rows(Invoice $invoice): array
    {
        return $invoice->invoices->map(function ($sentInvoice) use ($invoice) {
            $date = Carbon::parse($sentInvoice->date ?: $invoice->date);
            $invoiceNumber = $date->format('y').'/SUBS/'.$sentInvoice->sacre->code;

            if (! empty($sentInvoice->sacre->short_code)) {
                $invoiceNumber .= '/'.$sentInvoice->sacre->short_code;
            }

            $sentAt = $sentInvoice->sent_at ?: $sentInvoice->created_at;

            return [
                $sentInvoice->sacre->title,
                $invoiceNumber.'/'.$invoice->id,
                number_format($sentInvoice->totalAmount(), 2, '.', ''),
                $sentInvoice->batch,
                $sentAt ? Carbon::parse($sentAt)->toDateTimeString() : '',
                $sentInvoice->deliveryStatus(),
            ];
        })->all();
    }
}
