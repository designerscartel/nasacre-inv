<?php

namespace App\Actions\Invoice;

use App\Contracts\Invoice\SendsInvoiceInformation;
use App\Jobs\SendInvoice;
use App\Support\InvoiceSendPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

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
            'recipients' => ['required', 'array', 'min:1'],
            'recipients.*.sacre_id' => ['required', 'integer'],
            'recipients.*.contact_id' => ['required', 'integer'],
        ])->validateWithBag('sendInvoiceInformation');

        $sendPlan = new InvoiceSendPlan();
        $plan = $sendPlan->forInvoice($invoice);
        try {
            $recipients = $sendPlan->selectedRecipients($plan, $input['recipients']);
        } catch (InvalidArgumentException $exception) {
            $validator = Validator::make([], []);
            $validator->errors()->add('recipients', $exception->getMessage());

            throw new ValidationException($validator, null, 'sendInvoiceInformation');
        }

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
