<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\Sacre;
use App\Models\SacreContact;
use App\Models\SacreInvoice;
use App\Support\InvoiceSendPlan;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class InvoiceSendPlanTest extends TestCase
{
    public function test_it_reports_the_next_batch_recipients_and_skip_reasons()
    {
        $recipient = $this->sacre(1, 'Alpha SACRE', [
            new SacreContact(['id' => 10, 'email' => 'clerk@example.test', 'default' => true]),
        ]);
        $withoutContacts = $this->sacre(2, 'Beta SACRE', []);
        $withoutValidEmail = $this->sacre(3, 'Gamma SACRE', [
            new SacreContact(['email' => 'not-an-email']),
        ]);

        $plan = (new InvoiceSendPlan())->fromSacres(
            new Invoice(['batch' => 4]),
            new Collection([$recipient, $withoutContacts, $withoutValidEmail])
        );

        $this->assertSame(5, $plan['batch']);
        $this->assertSame(1, $plan['recipient_count']);
        $this->assertSame(2, $plan['skipped_count']);
        $this->assertSame('clerk@example.test', $plan['recipients']->first()['contact']->email);
        $this->assertSame([
            'No contacts are recorded.',
            'No contact has a valid email address.',
        ], $plan['skipped']->pluck('reason')->all());
    }

    public function test_it_uses_the_selected_sacres_and_contacts()
    {
        $firstContact = new SacreContact(['id' => 10, 'email' => 'first@example.test', 'default' => true]);
        $secondContact = new SacreContact(['id' => 11, 'email' => 'second@example.test']);
        $alpha = $this->sacre(1, 'Alpha SACRE', [$firstContact, $secondContact]);
        $beta = $this->sacre(2, 'Beta SACRE', [
            new SacreContact(['id' => 20, 'email' => 'beta@example.test']),
        ]);
        $sendPlan = new InvoiceSendPlan();
        $plan = $sendPlan->fromSacres(new Invoice(['batch' => 1]), new Collection([$alpha, $beta]));

        $recipients = $sendPlan->selectedRecipients($plan, [
            ['sacre_id' => 1, 'contact_id' => 11],
        ]);

        $this->assertCount(1, $recipients);
        $this->assertSame('Alpha SACRE', $recipients->first()['sacre']->title);
        $this->assertSame('second@example.test', $recipients->first()['contact']->email);
    }

    public function test_it_rejects_a_contact_from_another_sacre()
    {
        $alpha = $this->sacre(1, 'Alpha SACRE', [
            new SacreContact(['id' => 10, 'email' => 'alpha@example.test']),
        ]);
        $beta = $this->sacre(2, 'Beta SACRE', [
            new SacreContact(['id' => 20, 'email' => 'beta@example.test']),
        ]);
        $sendPlan = new InvoiceSendPlan();
        $plan = $sendPlan->fromSacres(new Invoice(), new Collection([$alpha, $beta]));

        $this->expectException(InvalidArgumentException::class);

        $sendPlan->selectedRecipients($plan, [
            ['sacre_id' => 1, 'contact_id' => 20],
        ]);
    }

    public function test_it_summarises_previous_batches_and_empty_queued_batches()
    {
        $invoice = new Invoice(['batch' => 3]);
        $invoice->setRelation('invoices', new Collection([
            new SacreInvoice(['batch' => 1, 'created_at' => '2026-10-01 10:00:00']),
            new SacreInvoice(['batch' => 1, 'created_at' => '2026-10-01 10:05:00']),
            new SacreInvoice(['batch' => 2, 'created_at' => '2026-10-02 11:00:00']),
        ]));

        $batches = (new InvoiceSendPlan())->previousBatches($invoice);

        $this->assertSame([1, 2, 3], $batches->pluck('batch')->all());
        $this->assertSame([2, 1, 0], $batches->pluck('recipient_count')->all());
        $this->assertNull($batches->last()['sent_at']);
    }

    public function test_an_unsent_invoice_has_no_previous_batches()
    {
        $invoice = new Invoice(['batch' => 0]);
        $invoice->setRelation('invoices', new Collection());
        $sendPlan = new InvoiceSendPlan();

        $this->assertFalse($sendPlan->hasBeenSent($invoice));
        $this->assertSame([], $sendPlan->previousBatches($invoice)->all());
    }

    private function sacre($id, $title, array $contacts)
    {
        $sacre = new Sacre(['id' => $id, 'title' => $title]);
        $sacre->setRelation('contacts', new Collection($contacts));

        return $sacre;
    }
}
