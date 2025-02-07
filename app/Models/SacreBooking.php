<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SacreBooking extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'po_number',
        'name',
        'email',
        'phone',
        'date',
        'sacre_id',
        'booking_id',
        'delegate_one_name',
        'delegate_one_email',
        'delegate_one_diet',
        'delegate_one_free',
        'delegate_two_name',
        'delegate_two_email',
        'delegate_two_diet',
        'delegate_two_free',
        'virtual_one_name',
        'virtual_one_email',
        'virtual_one_free',
        'virtual_two_name',
        'virtual_two_email',
        'virtual_two_free',
        'confirmed',
        'address',
        'offer'
    ];

    /**
     * Get the sacre it to.
     */
    public function sacre()
    {
        return $this->belongsTo(Sacre::class, 'sacre_id');
    }

    /**
     * Get the invoice it to.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }


    /**
     * Get all the booking delegates.
     */
    public function delegates()
    {
        return $this->hasMany(BookingDelegate::class,  'sacre_booking_id');
    }




    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\SacreBookingResource($this);
    }
}
