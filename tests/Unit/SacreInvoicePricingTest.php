<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\SacreInvoice;
use Tests\TestCase;

class SacreInvoicePricingTest extends TestCase
{
    public function test_it_adds_virtual_training_to_the_subscription()
    {
        $sacreInvoice = new SacreInvoice(['virtual_training' => true]);
        $sacreInvoice->setRelation('invoice', new Invoice(['subs' => 250]));

        $this->assertSame(310.0, $sacreInvoice->totalAmount());
    }

    public function test_it_uses_the_subscription_without_virtual_training()
    {
        $sacreInvoice = new SacreInvoice(['virtual_training' => false]);
        $sacreInvoice->setRelation('invoice', new Invoice(['subs' => 250]));

        $this->assertSame(250.0, $sacreInvoice->totalAmount());
    }

    public function test_an_override_replaces_the_calculated_total()
    {
        $sacreInvoice = new SacreInvoice([
            'virtual_training' => true,
            'total_override' => 275,
        ]);
        $sacreInvoice->setRelation('invoice', new Invoice(['subs' => 250]));

        $this->assertSame(275.0, $sacreInvoice->totalAmount());
    }

    public function test_it_uses_the_configured_training_price()
    {
        config(['invoices.training_price' => 75]);
        $sacreInvoice = new SacreInvoice(['virtual_training' => true]);
        $sacreInvoice->setRelation('invoice', new Invoice(['subs' => 185]));

        $this->assertSame(260.0, $sacreInvoice->totalAmount());
    }
}
