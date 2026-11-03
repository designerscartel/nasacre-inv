<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\Sacre;
use App\Models\SacreInvoice;
use App\Support\InvoiceCsvExporter;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class InvoiceCsvExporterTest extends TestCase
{
    public function test_it_exports_historical_sacres_and_delivery_details()
    {
        $sacre = new Sacre([
            'title' => 'Jersey',
            'code' => 61,
            'short_code' => 'JE',
        ]);
        $delivery = new SacreInvoice([
            'batch' => 2,
            'delivery_status' => 'sent',
            'virtual_training' => true,
        ]);
        $invoice = new Invoice([
            'id' => 6,
            'subs' => 185,
        ]);

        $sacre->setRawAttributes(array_merge($sacre->getAttributes(), [
            'deleted_at' => Carbon::parse('2023-03-17 12:09:20'),
        ]));
        $delivery->setRawAttributes(array_merge($delivery->getAttributes(), [
            'date' => Carbon::parse('2026-06-24 11:09:00'),
            'sent_at' => Carbon::parse('2026-06-26 08:49:04'),
        ]));
        $invoice->setRawAttributes(array_merge($invoice->getAttributes(), [
            'date' => Carbon::parse('2026-06-24 11:09:00'),
        ]));
        $delivery->setRelation('sacre', $sacre);
        $delivery->setRelation('invoice', $invoice);
        $invoice->setRelation('invoices', new Collection([$delivery]));

        $exporter = new InvoiceCsvExporter();

        $this->assertSame([
            'SACRE',
            'Invoice Number',
            'Amount',
            'Batch',
            'Sent Date',
            'Delivery Status',
        ], $exporter->header());
        $this->assertSame([[
            'Jersey',
            '26/SUBS/61/JE/6',
            '245.00',
            2,
            '2026-06-26 08:49:04',
            'sent',
        ]], $exporter->rows($invoice));
    }
}
