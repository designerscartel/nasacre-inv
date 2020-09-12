<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPosition extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
    ];

    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\ContactPositionResource($this);
    }
}
