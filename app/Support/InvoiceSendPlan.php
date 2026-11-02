<?php

namespace App\Support;

use App\Models\Sacre;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class InvoiceSendPlan
{
    public function forInvoice($invoice)
    {
        $invoice->loadMissing('invoices');

        return $this->fromSacres($invoice, Sacre::with('contacts')->orderBy('title')->get());
    }

    public function fromSacres($invoice, Collection $sacres)
    {
        $selector = new InvoiceContactSelector();
        $recipients = collect();
        $skipped = collect();

        $sacres->each(function ($sacre) use ($selector, $recipients, $skipped) {
            $contacts = $selector->eligible($sacre->contacts);
            $contact = $contacts->first();

            if ($contact) {
                $recipients->push(compact('sacre', 'contact', 'contacts'));
                return;
            }

            $skipped->push([
                'sacre_id' => $sacre->id,
                'sacre' => $sacre->title,
                'reason' => $sacre->contacts->isEmpty()
                    ? 'No contacts are recorded.'
                    : 'No contact has a valid email address.',
            ]);
        });

        return [
            'batch' => ((int) $invoice->batch) + 1,
            'has_been_sent' => ((int) $invoice->batch) > 0
                || ($invoice->relationLoaded('invoices') && $invoice->invoices->isNotEmpty()),
            'previous_batches' => $invoice->relationLoaded('invoices')
                ? $this->previousBatches($invoice)
                : collect(),
            'recipient_count' => $recipients->count(),
            'skipped_count' => $skipped->count(),
            'recipients' => $recipients,
            'skipped' => $skipped->values(),
        ];
    }

    public function preview($invoice)
    {
        $plan = $this->forInvoice($invoice);

        return [
            'batch' => $plan['batch'],
            'has_been_sent' => $plan['has_been_sent'],
            'previous_batches' => $plan['previous_batches'],
            'recipient_count' => $plan['recipient_count'],
            'skipped_count' => $plan['skipped_count'],
            'recipients' => $plan['recipients']->map(function ($recipient) {
                return [
                    'sacre_id' => $recipient['sacre']->id,
                    'sacre' => $recipient['sacre']->title,
                    'contact_id' => $recipient['contact']->id,
                    'contacts' => $recipient['contacts']->map(function ($contact) {
                        return [
                            'id' => $contact->id,
                            'name' => $contact->name,
                            'email' => $contact->email,
                        ];
                    })->values(),
                ];
            })->values(),
            'skipped' => $plan['skipped'],
        ];
    }

    public function previousBatches($invoice)
    {
        $invoicesByBatch = $invoice->invoices->groupBy('batch');
        $highestBatch = max((int) $invoice->batch, (int) $invoicesByBatch->keys()->max());

        if ($highestBatch < 1) {
            return collect();
        }

        return collect(range(1, $highestBatch))->map(function ($batch) use ($invoicesByBatch) {
            $invoices = $invoicesByBatch->get($batch, collect());

            return [
                'batch' => $batch,
                'recipient_count' => $invoices->count(),
                'sent_at' => optional($invoices->max('created_at'))->toIso8601String(),
            ];
        })->values();
    }

    public function hasBeenSent($invoice)
    {
        if ((int) $invoice->batch > 0) {
            return true;
        }

        return $invoice->relationLoaded('invoices')
            ? $invoice->invoices->isNotEmpty()
            : $invoice->invoices()->exists();
    }

    public function selectedRecipients(array $plan, array $selections)
    {
        $available = $plan['recipients']->keyBy(function ($recipient) {
            return (string) $recipient['sacre']->id;
        });
        $selectedSacreIds = collect($selections)->pluck('sacre_id')->map(function ($sacreId) {
            return (string) $sacreId;
        });

        if ($selectedSacreIds->duplicates()->isNotEmpty()) {
            throw new InvalidArgumentException('Each SACRE may only be selected once.');
        }

        return collect($selections)->map(function ($selection) use ($available) {
            $recipient = $available->get((string) $selection['sacre_id']);
            $contact = $recipient
                ? $recipient['contacts']->firstWhere('id', (int) $selection['contact_id'])
                : null;

            if (!$recipient || !$contact) {
                throw new InvalidArgumentException('A selected contact is not valid for that SACRE.');
            }

            return [
                'sacre' => $recipient['sacre'],
                'contact' => $contact,
            ];
        })->values();
    }
}
