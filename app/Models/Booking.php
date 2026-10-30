<?php

namespace App\Models;

use App\Http\Resources\BookingMemberSubResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'email',
        'subscribed',
        'none_subscribed',
        'additional_amount',
        'date',
        'year',
        'from',
        'message',
        'venue',
        'batch',
        'new',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'new' => 'boolean',
        'date' => 'datetime',
    ];

    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\BookingResource($this);
    }

    /**
     * Boot Function
     */
    public static function boot()
    {
        parent::boot();
        self::deleting(function ($booking) {
            $booking->bookings()->each(function ($sacreBooking) {
                $sacreBooking->delete();
            });
            $booking->subs()->delete();
            $booking->memberSubs()->delete();
        });
    }

    /**
     * Get all the bookings.
     */
    public function bookings()
    {
        return $this->hasMany(SacreBooking::class);
    }

    /**
     * Get all the subs for bookings.
     */
    public function subs()
    {
        return $this->hasMany(BookingSub::class)->orderBy('id');
    }

    /**
     * Get all the member subs for bookings.
     */
    public function memberSubs()
    {
        return $this->hasMany(BookingMemberSub::class)->orderBy('id');
    }


    /**
     * Get all the booking delegates.
     */
    public function delegates()
    {
        return $this->hasMany(BookingDelegate::class);
    }

    public function priceForDelegateCount(int $delegateCount, bool $member): ?float
    {
        if ($delegateCount < 1) {
            return null;
        }

        $prices = $member ? $this->memberSubs : $this->subs;
        $price = $prices->get($delegateCount - 1);

        return $price ? (float) $price->sub : null;
    }

}
