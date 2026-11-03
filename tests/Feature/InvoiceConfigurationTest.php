<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Sacre;
use Tests\TestCase;

class InvoiceConfigurationTest extends TestCase
{
    public function test_the_invoice_pdf_renders_configured_annual_values()
    {
        config([
            'invoices.training_price' => 75,
            'invoices.charity_number' => 'CHARITY-123',
            'invoices.payment_email' => 'payments@example.test',
            'invoices.bank.name' => 'Example Bank',
            'invoices.bank.account_name' => 'Example Account',
            'invoices.bank.account_number' => '12345678',
            'invoices.bank.sort_code' => '10-20-30',
        ]);
        $invoice = new Invoice([
            'subs' => 185,
            'date' => '2026-06-24',
            'year' => 2026,
        ]);
        $invoice->id = 6;
        $invoice->virtual_training = true;
        $invoice->total_override = null;
        $sacre = new Sacre([
            'title' => 'Example SACRE',
            'address' => 'Example Council',
            'short_code' => 'EX',
            'code' => 61,
        ]);

        $html = view('pdfs.2026-invoice', [
            'invoice' => $invoice,
            'sacre' => $sacre,
            'date' => $invoice->date,
        ])->render();

        $this->assertStringContainsString('Registered Charity Number: CHARITY-123', $html);
        $this->assertStringContainsString('&pound;75.00', $html);
        $this->assertStringContainsString('&pound;260.00', $html);
        $this->assertStringContainsString('payments@example.test', $html);
        $this->assertStringContainsString('Example Bank', $html);
        $this->assertStringContainsString('Example Account', $html);
        $this->assertStringContainsString('12345678', $html);
        $this->assertStringContainsString('10-20-30', $html);
    }
}
