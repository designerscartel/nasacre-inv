<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sacre extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'region_id',
        'title',
        'member',
        'address',
        'short_code',
        'code',
        'po',
        'finance',
    ];

    /**
     * Get the region of the Sacre.
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    /**
     * Get all of the contacts the sacre belongs to.
     */
    public function contacts()
    {
        return $this->hasMany(SacreContact::class);
    }

    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\SacreResource($this);
    }


}
