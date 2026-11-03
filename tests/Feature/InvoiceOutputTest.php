<?php

namespace Tests\Feature;

use App\Contracts\Invoice\CreatesInvoicePdf;
use App\Models\Invoice;
use App\Models\Sacre;
use App\Models\SacreInvoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceOutputTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_download_the_invoice_csv()
    {
        $this->actingAs(User::factory()->create());
        $invoice = $this->invoice();
        $sacre = $this->sacre();
        $delivery = $this->delivery($invoice, $sacre, [
            'batch' => 2,
            'delivery_status' => 'sent',
            'virtual_training' => true,
            'sent_at' => '2026-06-26 08:49:04',
        ]);

        $response = $this->get(route('invoices.list', $invoice));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="invoices.csv"');
        $rows = array_map('str_getcsv', preg_split('/\r\n|\r|\n/', trim($response->getContent())));
        $this->assertSame([
            'SACRE',
            'Invoice Number',
            'Amount',
            'Batch',
            'Sent Date',
            'Delivery Status',
        ], $rows[0]);
        $this->assertSame([
            'Example SACRE',
            '26/SUBS/61/EX/'.$invoice->id,
            '160.00',
            '2',
            '2026-06-26 08:49:04',
            'sent',
        ], $rows[1]);
        $this->assertSame($delivery->id, SacreInvoice::first()->id);
    }

    public function test_the_csv_includes_a_soft_deleted_sacre()
    {
        $this->actingAs(User::factory()->create());
        $invoice = $this->invoice();
        $sacre = $this->sacre(['title' => 'Historical SACRE']);
        $this->delivery($invoice, $sacre);
        $sacre->delete();

        $response = $this->get(route('invoices.list', $invoice));

        $response->assertOk();
        $this->assertStringContainsString('Historical SACRE', $response->getContent());
    }

    public function test_the_pdf_receives_sacre_invoice_overrides()
    {
        $this->actingAs(User::factory()->create());
        $invoice = $this->invoice();
        $sacre = $this->sacre(['address' => 'Original address']);
        $delivery = $this->delivery($invoice, $sacre, [
            'po_number' => 'PO-OVERRIDE',
            'address' => 'Override address',
            'date' => '2027-05-06',
            'virtual_training' => true,
            'total_override' => '250.75',
        ]);
        $this->mock(CreatesInvoicePdf::class, function ($mock) {
            $mock->shouldReceive('inline')->once()->withArgs(function ($pdfInvoice, $pdfSacre) {
                $this->assertSame('PO-OVERRIDE', $pdfInvoice->po_number);
                $this->assertSame('2027-05-06', $pdfInvoice->date->format('Y-m-d'));
                $this->assertTrue($pdfInvoice->virtual_training);
                $this->assertSame('250.75', $pdfInvoice->total_override);
                $this->assertSame('Example SACRE', $pdfSacre->title);
                $this->assertSame('Override address', $pdfSacre->address);

                return true;
            })->andReturn(response('pdf contents', 200, ['Content-Type' => 'application/pdf']));
        });

        $response = $this->get(route('sacre-invoices.pdf', [$sacre, $delivery]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertSee('pdf contents');
    }

    public function test_the_pdf_uses_base_values_without_overrides()
    {
        $this->actingAs(User::factory()->create());
        $invoice = $this->invoice();
        $sacre = $this->sacre([
            'address' => 'Original address',
            'po' => 'SACRE-PO',
            'virtual_training' => true,
        ]);
        $delivery = $this->delivery($invoice, $sacre, [
            'po_number' => null,
            'address' => null,
            'date' => null,
            'virtual_training' => null,
            'total_override' => null,
        ]);
        $this->mock(CreatesInvoicePdf::class, function ($mock) use ($invoice, $sacre) {
            $mock->shouldReceive('inline')->once()->withArgs(function ($pdfInvoice, $pdfSacre) use ($invoice, $sacre) {
                $this->assertSame($invoice->date->format('Y-m-d'), $pdfInvoice->date->format('Y-m-d'));
                $this->assertNull($pdfInvoice->po_number);
                $this->assertNull($pdfInvoice->virtual_training);
                $this->assertNull($pdfInvoice->total_override);
                $this->assertSame($sacre->address, $pdfSacre->address);

                return true;
            })->andReturn(response('pdf contents'));
        });

        $response = $this->get(route('sacre-invoices.pdf', [$sacre, $delivery]));

        $response->assertOk();
    }

    private function invoice()
    {
        return Invoice::create([
            'email' => 'accounts@example.test',
            'subs' => 100,
            'date' => '2026-06-24 11:09:00',
            'year' => 2026,
            'from' => 'NASACRE',
            'message' => 'Invoice message.',
            'batch' => 2,
        ]);
    }

    private function sacre(array $attributes = [])
    {
        return Sacre::create(array_merge([
            'region_id' => 1,
            'title' => 'Example SACRE',
            'member' => true,
            'address' => 'Example Council',
            'short_code' => 'EX',
            'code' => 61,
            'virtual_training' => false,
        ], $attributes));
    }

    private function delivery($invoice, $sacre, array $attributes = [])
    {
        return SacreInvoice::create(array_merge([
            'invoice_id' => $invoice->id,
            'sacre_id' => $sacre->id,
            'batch' => 1,
            'delivery_status' => 'queued',
            'virtual_training' => false,
        ], $attributes));
    }
}
