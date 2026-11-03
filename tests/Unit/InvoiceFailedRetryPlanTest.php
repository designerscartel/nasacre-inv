<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\Sacre;
use App\Models\SacreInvoice;
use App\Support\InvoiceFailedRetryPlan;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class InvoiceFailedRetryPlanTest extends TestCase
{
    public function test_it_previews_only_failed_deliveries_from_the_latest_batch()
    {
        $invoice = new Invoice(['batch' => 2]);
        $invoice->setRelation('invoices', new Collection([
            $this->delivery(1, 1, 'failed', 'old@example.test'),
            $this->delivery(2, 2, 'sent', 'sent@example.test'),
            $this->delivery(3, 2, 'queued', 'queued@example.test'),
            $this->delivery(4, 2, 'failed', 'failed@example.test', 'Mailbox unavailable'),
        ]));

        $preview = (new InvoiceFailedRetryPlan())->preview($invoice);

        $this->assertSame(2, $preview['batch']);
        $this->assertSame(1, $preview['failed_count']);
        $this->assertSame(4, $preview['recipients'][0]['id']);
        $this->assertSame('failed@example.test', $preview['recipients'][0]['email']);
        $this->assertSame('Mailbox unavailable', $preview['recipients'][0]['failure_message']);
    }

    public function test_it_uses_the_highest_recorded_batch()
    {
        $invoice = new Invoice(['batch' => 1]);
        $invoice->setRelation('invoices', new Collection([
            $this->delivery(1, 2, 'failed', 'latest@example.test'),
        ]));

        $preview = (new InvoiceFailedRetryPlan())->preview($invoice);

        $this->assertSame(2, $preview['batch']);
        $this->assertSame(1, $preview['failed_count']);
    }

    private function delivery($id, $batch, $status, $email, $failureMessage = null)
    {
        $delivery = new SacreInvoice([
            'id' => $id,
            'batch' => $batch,
            'delivery_status' => $status,
            'recipient_email' => $email,
            'failure_message' => $failureMessage,
        ]);
        $delivery->setRelation('sacre', new Sacre(['title' => 'SACRE '.$id]));

        return $delivery;
    }
}
