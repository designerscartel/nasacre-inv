<?php

namespace App\Actions\Invoice;

use App\Contracts\Invoice\RetriesFailedInvoiceDeliveries;
use App\Jobs\SendInvoice;
use App\Models\SacreInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RetryFailedInvoiceDeliveries implements RetriesFailedInvoiceDeliveries
{
    public function retry($invoice, array $input)
    {
        Validator::make($input, [
            'confirm_retry' => ['accepted'],
        ], [
            'confirm_retry.accepted' => 'You must confirm that the failed deliveries should be retried.',
        ])->validateWithBag('retryFailedInvoiceDeliveries');

        $deliveries = DB::transaction(function () use ($invoice) {
            $lockedInvoice = $invoice->newQuery()->lockForUpdate()->findOrFail($invoice->id);
            $latestBatch = max(
                (int) $lockedInvoice->batch,
                (int) SacreInvoice::where('invoice_id', $lockedInvoice->id)->max('batch')
            );

            $deliveries = SacreInvoice::with('sacre')
                ->where('invoice_id', $lockedInvoice->id)
                ->where('batch', $latestBatch)
                ->where('delivery_status', 'failed')
                ->lockForUpdate()
                ->get();

            $deliveries->each(function ($delivery) {
                $delivery->update([
                    'delivery_status' => 'queued',
                    'queued_at' => now(),
                    'failed_at' => null,
                    'failure_message' => null,
                ]);
            });

            return $deliveries;
        });

        $deliveries->each(function ($delivery, $iteration) use ($invoice) {
            SendInvoice::dispatch([
                'sacre' => $delivery->sacre,
                'invoice' => $invoice,
                'recipient_name' => $delivery->recipient_name,
                'recipient_email' => $delivery->recipient_email,
                'batch' => $delivery->batch,
                'sacre_invoice_id' => $delivery->id,
            ])->delay(now()->addSeconds($iteration * 5));
        });
    }
}
