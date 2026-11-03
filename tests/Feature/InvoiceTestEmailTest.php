<?php

namespace Tests\Feature;

use App\Contracts\Invoice\CreatesInvoicePdf;
use App\Mail\InvoiceForQueuing;
use App\Models\Invoice;
use App\Models\Sacre;
use App\Models\SacreInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvoiceTestEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_send_a_test_invoice_without_delivery_side_effects()
    {
        Mail::fake();
        $this->actingAs(User::factory()->create());
        $invoice = $this->invoice();
        $sacre = $this->sacre();
        $this->mock(CreatesInvoicePdf::class, function ($mock) use ($invoice, $sacre) {
            $mock->shouldReceive('output')->once()->withArgs(function ($pdfInvoice, $pdfSacre) use ($invoice, $sacre) {
                return $pdfInvoice->is($invoice) && $pdfSacre->is($sacre);
            })->andReturn('test pdf contents');
        });

        $response = $this->post(route('invoices.send-test', $invoice), [
            'sacre_id' => $sacre->id,
            'email' => 'reviewer@example.test',
        ]);

        $response->assertStatus(303);
        Mail::assertSent(InvoiceForQueuing::class, function ($mail) {
            $mail->build();

            return $mail->hasTo('reviewer@example.test')
                && empty($mail->cc)
                && $mail->subject === '[TEST] NASACRE Membership Invoice Example SACRE 2026';
        });
        $this->assertSame(4, $invoice->refresh()->batch);
        $this->assertSame(0, SacreInvoice::count());
    }

    public function test_test_invoice_validation_rejects_invalid_input_without_sending_mail()
    {
        Mail::fake();
        $this->actingAs(User::factory()->create());
        $invoice = $this->invoice();

        $response = $this->from(route('invoices.show', $invoice))->post(route('invoices.send-test', $invoice), [
            'sacre_id' => 999,
            'email' => 'not-an-email',
        ]);

        $response->assertRedirect(route('invoices.show', $invoice));
        $response->assertSessionHasErrors(['sacre_id', 'email'], null, 'sendTestInvoice');
        Mail::assertNothingSent();
        $this->assertSame(4, $invoice->refresh()->batch);
        $this->assertSame(0, SacreInvoice::count());
    }

    public function test_a_guest_cannot_send_a_test_invoice()
    {
        Mail::fake();
        $invoice = $this->invoice();
        $sacre = $this->sacre();

        $response = $this->post(route('invoices.send-test', $invoice), [
            'sacre_id' => $sacre->id,
            'email' => 'reviewer@example.test',
        ]);

        $response->assertRedirect('/login');
        Mail::assertNothingSent();
    }

    private function invoice()
    {
        return Invoice::create([
            'email' => 'accounts@example.test',
            'subs' => 185,
            'date' => '2026-06-24',
            'year' => 2026,
            'from' => 'NASACRE',
            'message' => 'Please find the invoice attached.',
            'batch' => 4,
        ]);
    }

    private function sacre()
    {
        return Sacre::create([
            'region_id' => 1,
            'title' => 'Example SACRE',
            'member' => true,
            'address' => 'Example Council',
            'short_code' => 'EX',
            'code' => 61,
            'finance' => 'finance@example.test',
        ]);
    }
}
