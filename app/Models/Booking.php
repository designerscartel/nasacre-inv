<?php

namespace App\Models;

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
        'additional',
        'date',
        'year',
        'from',
        'message',
        'venue',
        'batch'
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


}
