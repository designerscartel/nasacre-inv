<?php

namespace Tests\Feature;

use App\Actions\Invoice\RetryFailedInvoiceDeliveries;
use App\Contracts\Invoice\CreatesInvoicePdf;
use App\Jobs\SendInvoice;
use App\Mail\InvoiceForQueuing;
use App\Models\Invoice;
use App\Models\Sacre;
use App\Models\SacreInvoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use Tests\TestCase;

class InvoiceDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_queued_invoice_is_mailed_and_marked_as_sent()
    {
        Mail::fake();
        $this->stubPdf();
        list($invoice, $sacre, $delivery) = $this->delivery();

        $this->job($invoice, $sacre, $delivery)->handle();

        Mail::assertSent(InvoiceForQueuing::class, function ($mail) {
            return $mail->hasTo('recipient@example.test')
                && $mail->hasCc('finance@example.test');
        });
        $delivery->refresh();
        $this->assertSame('sent', $delivery->delivery_status);
        $this->assertNotNull($delivery->sent_at);
        $this->assertNull($delivery->failed_at);
        $this->assertNull($delivery->failure_message);
    }

    public function test_a_failed_invoice_is_recorded_for_retry()
    {
        $this->stubPdf();
        Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('Mailbox unavailable'));
        list($invoice, $sacre, $delivery) = $this->delivery();
        $job = $this->job($invoice, $sacre, $delivery);

        try {
            $job->handle();
            $this->fail('The mail transport exception was not thrown.');
        } catch (RuntimeException $exception) {
            $job->failed($exception);
        }

        $delivery->refresh();
        $this->assertSame('failed', $delivery->delivery_status);
        $this->assertNotNull($delivery->failed_at);
        $this->assertSame('Mailbox unavailable', $delivery->failure_message);
        $this->assertNull($delivery->sent_at);
    }

    public function test_retry_queues_only_failed_deliveries_from_the_latest_batch()
    {
        Queue::fake();
        list($invoice, $sacre, $failed) = $this->delivery('failed', 2);
        $sent = $this->createDelivery($invoice, $sacre, 'sent', 2, 'sent@example.test');
        $oldFailure = $this->createDelivery($invoice, $sacre, 'failed', 1, 'old@example.test');

        (new RetryFailedInvoiceDeliveries())->retry($invoice, ['confirm_retry' => '1']);

        Queue::assertPushed(SendInvoice::class, 1);
        Queue::assertPushed(SendInvoice::class, function ($job) use ($failed) {
            return $this->jobDeliveryId($job) === $failed->id;
        });
        $this->assertSame('queued', $failed->refresh()->delivery_status);
        $this->assertSame('sent', $sent->refresh()->delivery_status);
        $this->assertSame('failed', $oldFailure->refresh()->delivery_status);
    }

    public function test_retrying_again_does_not_duplicate_an_already_queued_delivery()
    {
        Queue::fake();
        list($invoice) = $this->delivery('failed', 2);
        $action = new RetryFailedInvoiceDeliveries();

        $action->retry($invoice, ['confirm_retry' => '1']);
        $action->retry($invoice, ['confirm_retry' => '1']);

        Queue::assertPushed(SendInvoice::class, 1);
    }

    private function delivery($status = 'queued', $batch = 1)
    {
        $invoice = Invoice::create([
            'email' => 'accounts@example.test',
            'subs' => 100,
            'date' => now(),
            'year' => 2026,
            'from' => 'NASACRE',
            'message' => 'Please find your invoice attached.',
            'batch' => $batch,
        ]);
        $sacre = Sacre::create([
            'region_id' => 1,
            'title' => 'Example SACRE',
            'member' => true,
            'short_code' => 'EX',
            'code' => 1,
            'finance' => 'finance@example.test',
        ]);

        return [$invoice, $sacre, $this->createDelivery($invoice, $sacre, $status, $batch)];
    }

    private function createDelivery($invoice, $sacre, $status, $batch, $email = 'recipient@example.test')
    {
        return SacreInvoice::create([
            'invoice_id' => $invoice->id,
            'sacre_id' => $sacre->id,
            'batch' => $batch,
            'delivery_status' => $status,
            'recipient_name' => 'Invoice Recipient',
            'recipient_email' => $email,
            'queued_at' => now(),
            'failed_at' => $status === 'failed' ? now() : null,
            'failure_message' => $status === 'failed' ? 'Previous failure' : null,
        ]);
    }

    private function job($invoice, $sacre, $delivery)
    {
        return new SendInvoice([
            'invoice' => $invoice,
            'sacre' => $sacre,
            'recipient_name' => $delivery->recipient_name,
            'recipient_email' => $delivery->recipient_email,
            'batch' => $delivery->batch,
            'sacre_invoice_id' => $delivery->id,
        ]);
    }

    private function stubPdf()
    {
        $this->mock(CreatesInvoicePdf::class, function ($mock) {
            $mock->shouldReceive('output')->andReturn('pdf contents');
        });
    }

    private function jobDeliveryId($job)
    {
        $details = (function () {
            return $this->details;
        })->call($job);

        return $details['sacre_invoice_id'];
    }
}
