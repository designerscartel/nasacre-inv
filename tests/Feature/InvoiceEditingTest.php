<?php

namespace Tests\Feature;

use App\Contracts\Invoice\CreatesInvoicePdf;
use App\Models\Invoice;
use App\Models\Sacre;
use App\Models\SacreInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceEditingTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_edit_an_annual_invoice()
    {
        $this->actingAs(User::factory()->create());
        $invoice = $this->invoice();

        $response = $this->put(route('invoices.update', $invoice), [
            'email' => 'billing@example.test',
            'subs' => '175.50',
            'date' => '2027-04-05',
            'from' => 'NASACRE Accounts',
            'message' => 'Updated invoice message.',
        ]);

        $response->assertStatus(303);
        $invoice->refresh();
        $this->assertSame('billing@example.test', $invoice->email);
        $this->assertSame('175.50', $invoice->subs);
        $this->assertSame('2027-04-05', $invoice->date->format('Y-m-d'));
        $this->assertSame(2027, $invoice->year);
        $this->assertSame('NASACRE Accounts', $invoice->from);
        $this->assertSame('Updated invoice message.', $invoice->message);
    }

    public function test_invalid_annual_invoice_edits_do_not_change_the_record()
    {
        $this->actingAs(User::factory()->create());
        $invoice = $this->invoice();

        $response = $this->from(route('invoices.show', $invoice))->put(route('invoices.update', $invoice), [
            'email' => 'not-an-email',
            'subs' => '-1',
            'date' => 'not-a-date',
            'from' => '',
            'message' => '',
        ]);

        $response->assertRedirect(route('invoices.show', $invoice));
        $response->assertSessionHasErrors(
            ['email', 'subs', 'date', 'from', 'message'],
            null,
            'updateInvoiceInformation'
        );
        $invoice->refresh();
        $this->assertSame('accounts@example.test', $invoice->email);
        $this->assertSame('100.00', $invoice->subs);
        $this->assertSame(2026, $invoice->year);
    }

    public function test_an_authenticated_user_can_edit_sacre_invoice_overrides()
    {
        $this->actingAs(User::factory()->create());
        list($sacre, $sacreInvoice) = $this->sacreInvoice();

        $response = $this->put(route('sacre-invoices.update', [$sacre, $sacreInvoice]), [
            'po_number' => 'PO-2027-15',
            'address' => "Finance Office\nExample Council",
            'date' => '2027-05-06',
            'virtual_training' => true,
            'total_override' => '250.75',
        ]);

        $response->assertStatus(303);
        $sacreInvoice->refresh();
        $this->assertSame('PO-2027-15', $sacreInvoice->po_number);
        $this->assertSame("Finance Office\nExample Council", $sacreInvoice->address);
        $this->assertSame('2027-05-06', $sacreInvoice->date->format('Y-m-d'));
        $this->assertTrue($sacreInvoice->virtual_training);
        $this->assertSame('250.75', $sacreInvoice->total_override);
    }

    public function test_a_sacre_invoice_cannot_be_edited_through_another_sacre()
    {
        $this->actingAs(User::factory()->create());
        list($owner, $sacreInvoice) = $this->sacreInvoice();
        $otherSacre = $this->sacre(['title' => 'Other SACRE', 'code' => 2]);

        $response = $this->put(route('sacre-invoices.update', [$otherSacre, $sacreInvoice]), [
            'po_number' => 'UNAUTHORISED',
            'virtual_training' => true,
            'total_override' => 1,
        ]);

        $response->assertNotFound();
        $sacreInvoice->refresh();
        $this->assertSame($owner->id, $sacreInvoice->sacre_id);
        $this->assertSame('ORIGINAL-PO', $sacreInvoice->po_number);
        $this->assertFalse($sacreInvoice->virtual_training);
        $this->assertNull($sacreInvoice->total_override);
    }

    public function test_a_sacre_invoice_pdf_cannot_be_viewed_through_another_sacre()
    {
        $this->actingAs(User::factory()->create());
        list(, $sacreInvoice) = $this->sacreInvoice();
        $otherSacre = $this->sacre(['title' => 'Other SACRE', 'code' => 2]);
        $this->mock(CreatesInvoicePdf::class, function ($mock) {
            $mock->shouldNotReceive('inline');
        });

        $response = $this->get(route('sacre-invoices.pdf', [$otherSacre, $sacreInvoice]));

        $response->assertNotFound();
    }

    private function sacreInvoice()
    {
        $sacre = $this->sacre();
        $sacreInvoice = SacreInvoice::create([
            'invoice_id' => $this->invoice()->id,
            'sacre_id' => $sacre->id,
            'batch' => 1,
            'po_number' => 'ORIGINAL-PO',
            'virtual_training' => false,
        ]);

        return [$sacre, $sacreInvoice];
    }

    private function invoice()
    {
        return Invoice::create([
            'email' => 'accounts@example.test',
            'subs' => 100,
            'date' => '2026-04-01',
            'year' => 2026,
            'from' => 'NASACRE',
            'message' => 'Original invoice message.',
            'batch' => 1,
        ]);
    }

    private function sacre(array $attributes = [])
    {
        return Sacre::create(array_merge([
            'region_id' => 1,
            'title' => 'Example SACRE',
            'member' => true,
            'short_code' => 'EX',
            'code' => 1,
        ], $attributes));
    }
}
