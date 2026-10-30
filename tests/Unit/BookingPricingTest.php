<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\BookingMemberSub;
use App\Models\BookingSub;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class BookingPricingTest extends TestCase
{
    public function test_it_selects_the_configured_price_tier_for_delegate_count()
    {
        $booking = new Booking();
        $booking->setRelation('subs', new Collection([
            new BookingSub(['sub' => 100]),
            new BookingSub(['sub' => 180]),
        ]));
        $booking->setRelation('memberSubs', new Collection([
            new BookingMemberSub(['sub' => 80]),
            new BookingMemberSub(['sub' => 140]),
        ]));

        $this->assertSame(100.0, $booking->priceForDelegateCount(1, false));
        $this->assertSame(180.0, $booking->priceForDelegateCount(2, false));
        $this->assertSame(80.0, $booking->priceForDelegateCount(1, true));
        $this->assertSame(140.0, $booking->priceForDelegateCount(2, true));
    }

    public function test_it_rejects_missing_price_tiers()
    {
        $booking = new Booking();
        $booking->setRelation('subs', new Collection([
            new BookingSub(['sub' => 100]),
        ]));
        $booking->setRelation('memberSubs', new Collection());

        $this->assertNull($booking->priceForDelegateCount(0, false));
        $this->assertNull($booking->priceForDelegateCount(2, false));
        $this->assertNull($booking->priceForDelegateCount(1, true));
    }
}
