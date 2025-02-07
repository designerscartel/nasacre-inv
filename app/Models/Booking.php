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
        'new'
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
        self::deleting(function ($booking) { // before delete() method call this

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
        return $this->hasMany(BookingSub::class);
    }

    /**
     * Get all the member subs for bookings.
     */
    public function memberSubs()
    {
        return $this->hasMany(BookingMemberSub::class);
    }


    /**
     * Get all the booking delegates.
     */
    public function delegates()
    {
        return $this->hasMany(BookingDelegate::class);
    }

}
