<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
    ];

    public function places(): BelongsToMany
    {
        return $this->belongsToMany(PlaceOfInterest::class);
    }

    public function sacres(): HasMany
    {
        return $this->hasMany(Sacre::class);
    }

    /**
     * Return a Resource
     */
    public function toResource(?string $resourceClass = null): \Illuminate\Http\Resources\Json\JsonResource
    {
        return new \App\Http\Resources\RegionResource($this);
    }
}
