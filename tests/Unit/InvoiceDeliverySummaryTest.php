<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\Sacre;
use App\Models\SacreInvoice;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class InvoiceDeliverySummaryTest extends TestCase
{
    public function test_it_summarises_the_latest_batch_delivery_states()
    {
        $invoice = new Invoice(['batch' => 2]);
        $invoice->setRelation('invoices', new Collection([
            $this->delivery(1, 1, 'sent', 'alpha@example.test'),
            $this->delivery(2, 2, 'sent', 'beta@example.test'),
            $this->delivery(3, 2, 'queued', 'gamma@example.test'),
            $this->delivery(4, 2, 'failed', 'delta@example.test', 'Mailbox unavailable'),
        ]));

        $summary = $invoice->latestDeliverySummary();

        $this->assertSame(2, $summary['batch']);
        $this->assertSame(3, $summary['total']);
        $this->assertSame(1, $summary['sent']);
        $this->assertSame(1, $summary['queued']);
        $this->assertSame(1, $summary['failed']);
        $this->assertSame('Mailbox unavailable', $summary['recipients']->last()['failure_message']);
    }

    public function test_legacy_records_without_a_status_are_treated_as_sent()
    {
        $delivery = new SacreInvoice();

        $this->assertSame('sent', $delivery->deliveryStatus());
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
