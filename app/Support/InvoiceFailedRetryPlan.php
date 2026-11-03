<?php

namespace App\Support;

use App\Models\Invoice;

class InvoiceFailedRetryPlan
{
    public function failedDeliveries(Invoice $invoice)
    {
        $latestBatch = max((int) $invoice->batch, (int) $invoice->invoices->max('batch'));

        return $invoice->invoices
            ->filter(function ($delivery) use ($latestBatch) {
                return (int) $delivery->batch === $latestBatch
                    && $delivery->deliveryStatus() === 'failed';
            })
            ->values();
    }

    public function preview(Invoice $invoice): array
    {
        $deliveries = $this->failedDeliveries($invoice);
        $latestBatch = max((int) $invoice->batch, (int) $invoice->invoices->max('batch'));

        return [
            'batch' => $latestBatch,
            'failed_count' => $deliveries->count(),
            'recipients' => $deliveries->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'sacre' => optional($delivery->sacre)->title,
                    'email' => $delivery->recipient_email,
                    'failure_message' => $delivery->failure_message,
                ];
            })->all(),
        ];
    }
}
