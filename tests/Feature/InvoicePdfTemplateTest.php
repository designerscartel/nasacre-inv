<?php

namespace Tests\Feature;

use App\Actions\Invoice\CreateInvoicePdf;
use App\Models\Invoice;
use App\Models\Sacre;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as Dompdf;
use Carbon\Carbon;
use Tests\TestCase;

class InvoicePdfTemplateTest extends TestCase
{
    public function test_it_uses_the_default_invoice_pdf_template()
    {
        config(['invoices.pdf_template' => 'pdfs.2026-invoice']);
        list($invoice, $sacre) = $this->invoiceData();
        $pdf = \Mockery::mock(Dompdf::class);
        Pdf::shouldReceive('loadView')->once()->withArgs(function ($view, $data) use ($invoice, $sacre) {
            return $view === 'pdfs.2026-invoice'
                && $data['invoice'] === $invoice
                && $data['sacre'] === $sacre
                && $data['date'] instanceof Carbon;
        })->andReturn($pdf);

        $result = (new CreateInvoicePdf())->create($invoice, $sacre);

        $this->assertSame($pdf, $result);
    }

    public function test_it_uses_the_configured_invoice_pdf_template()
    {
        config(['invoices.pdf_template' => 'pdfs.invoice']);
        list($invoice, $sacre) = $this->invoiceData();
        $pdf = \Mockery::mock(Dompdf::class);
        Pdf::shouldReceive('loadView')->once()->with('pdfs.invoice', \Mockery::type('array'))->andReturn($pdf);

        $result = (new CreateInvoicePdf())->create($invoice, $sacre);

        $this->assertSame($pdf, $result);
    }

    private function invoiceData()
    {
        return [
            new Invoice([
                'date' => '2026-06-24',
                'year' => 2026,
                'subs' => 100,
            ]),
            new Sacre([
                'title' => 'Example SACRE',
                'short_code' => 'EX',
                'code' => 61,
            ]),
        ];
    }
}
