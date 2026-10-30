<?php

namespace App\Actions\Invoice;

use App\Contracts\Invoice\SendsInvoiceInformation;
use App\Jobs\SendInvoice;
use App\Models\Sacre;
use App\Support\InvoiceContactSelector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Class SendInvoiceInformation
 * @package App\Actions\Invoice
 */
class SendInvoiceInformation implements SendsInvoiceInformation
{
    /**
     * Validate and Sends the given invoice's information.
     *
     * @param mixed $invoice
     * @param array $input
     * @return void
     */
    public function send($invoice, array $input)
    {

        Validator::make($input, [
            'send' => ['required'],
        ])->validateWithBag('sendInvoiceInformation');

        $selector = new InvoiceContactSelector();
        $recipients = Sacre::with('contacts')->get()->map(function ($sacre) use ($selector) {
            $contact = $selector->select($sacre->contacts);

            return $contact ? compact('sacre', 'contact') : null;
        })->filter()->values();

        if ($recipients->isEmpty()) {
            return;
        }

        $batch = DB::transaction(function () use ($invoice) {
            $lockedInvoice = $invoice->newQuery()->lockForUpdate()->findOrFail($invoice->id);
            $lockedInvoice->batch = ((int) $lockedInvoice->batch) + 1;
            $lockedInvoice->save();

            return $lockedInvoice->batch;
        });

        $recipients->each(function ($recipient, $iteration) use ($invoice, $batch) {
            SendInvoice::dispatch([
                'sacre' => $recipient['sacre'],
                'invoice' => $invoice,
                'contact' => $recipient['contact'],
                'batch' => $batch,
            ])->delay(now()->addSeconds($iteration * 5));
        });

    }


}
