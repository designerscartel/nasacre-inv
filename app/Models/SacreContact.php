<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SacreContact extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'sacre_id',
        'position_id',
        'name',
        'email',
        'tel',
        'role_dscpn',
        'default'
    ];

    /**
     * Get the SACRE it belongs to.
     */
    public function sacre()
    {
        return $this->belongsTo(Sacre::class, 'sacre_id');
    }

    /**
     * Get the Position it belongs to.
     */
    public function position()
    {
        return $this->belongsTo(ContactPosition::class, 'position_id');
    }

    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\SacreContactResource($this);
    }
}
