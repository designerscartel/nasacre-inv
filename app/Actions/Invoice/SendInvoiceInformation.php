<?php

namespace App\Actions\Invoice;

use App\Contracts\Invoice\SendsInvoiceInformation;
use App\Jobs\SendInvoice;
use App\Models\SacreInvoice;
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
            'confirm_resend' => ['nullable'],
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

        $batch = DB::transaction(function () use ($invoice, $input) {
            $lockedInvoice = $invoice->newQuery()->lockForUpdate()->findOrFail($invoice->id);
            $hasBeenSent = (new InvoiceSendPlan())->hasBeenSent($lockedInvoice);

            Validator::make($input, [
                'confirm_resend' => $hasBeenSent ? ['accepted'] : ['nullable'],
            ], [
                'confirm_resend.accepted' => 'You must confirm that this invoice is being resent.',
            ])->validateWithBag('sendInvoiceInformation');

            $lockedInvoice->batch = ((int) $lockedInvoice->batch) + 1;
            $lockedInvoice->save();

            return $lockedInvoice->batch;
        });

        $recipients->each(function ($recipient, $iteration) use ($invoice, $batch) {
            $sacreInvoice = SacreInvoice::updateOrCreate(
                [
                    'sacre_id' => $recipient['sacre']->id,
                    'invoice_id' => $invoice->id,
                    'batch' => $batch,
                ],
                [
                    'po_number' => $recipient['sacre']->po,
                    'virtual_training' => (bool) $recipient['sacre']->virtual_training,
                    'delivery_status' => 'queued',
                    'recipient_name' => $recipient['contact']->name,
                    'recipient_email' => $recipient['contact']->email,
                    'queued_at' => now(),
                    'sent_at' => null,
                    'failed_at' => null,
                    'failure_message' => null,
                ]
            );

            SendInvoice::dispatch([
                'sacre' => $recipient['sacre'],
                'invoice' => $invoice,
                'recipient_name' => $recipient['contact']->name,
                'recipient_email' => $recipient['contact']->email,
                'batch' => $batch,
                'sacre_invoice_id' => $sacreInvoice->id,
            ])->delay(now()->addSeconds($iteration * 5));
        });

    }


}
