<?php

namespace Tests\Unit;

use App\Models\SacreContact;
use App\Support\InvoiceContactSelector;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class InvoiceContactSelectorTest extends TestCase
{
    public function test_it_selects_a_valid_default_contact()
    {
        $contacts = new Collection([
            new SacreContact(['email' => 'clerk@example.test', 'position_id' => 2]),
            new SacreContact(['email' => 'default@example.test', 'default' => true]),
        ]);

        $contact = (new InvoiceContactSelector())->select($contacts);

        $this->assertSame('default@example.test', $contact->email);
    }

    public function test_it_skips_invalid_defaults_and_uses_position_priority()
    {
        $contacts = new Collection([
            new SacreContact(['email' => 'invalid', 'default' => true]),
            new SacreContact(['email' => 'position-three@example.test', 'position_id' => 3]),
            new SacreContact(['email' => 'position-two@example.test', 'position_id' => 2]),
        ]);

        $contact = (new InvoiceContactSelector())->select($contacts);

        $this->assertSame('position-two@example.test', $contact->email);
    }

    public function test_it_returns_null_without_a_valid_email()
    {
        $contacts = new Collection([
            new SacreContact(['email' => '']),
            new SacreContact(['email' => 'not-an-email']),
        ]);

        $this->assertNull((new InvoiceContactSelector())->select($contacts));
    }
}
