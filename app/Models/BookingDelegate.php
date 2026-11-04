<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingDelegate extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'booking_id',
        'sacre_id',
        'sacre_booking_id',
        'name',
        'email',
        'diet',
    ];

    /**
     * Return a Resource
     */
    public function toResource(?string $resourceClass = null): \Illuminate\Http\Resources\Json\JsonResource
    {
        return new \App\Http\Resources\BookingDelegateResource($this);
    }

    /**
     * Get all the bookings.
     */
    public function booking()
    {
        return $this->belongsTo(SacreBooking::class, 'sacre_booking_id');
    }


    /**
     * Get all the bookings.
     */
    public function sacre()
    {
        return $this->belongsTo(Sacre::class, 'sacre_id');
    }

}
