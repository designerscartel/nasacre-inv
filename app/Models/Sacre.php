<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sacre extends Model
{
    use HasFactory;


    protected $with = ['region'];

    /**
     * Get the owner of the team.
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }


}
