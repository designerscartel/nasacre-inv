<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SharedFile extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'sacre_id',
        'filename',
    ];

    /**
     * Get the SACRE it belongs to.
     */
    public function sacre()
    {
        return $this->belongsTo(Sacre::class, 'sacre_id');
    }

    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\SharedFileResource($this);
    }

    /**
     * Search Sacre
     */
    public function scopeSearch($query, $term)
    {
        $query->when($term ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('filename', 'like', '%'.$search.'%');
            });
        });
    }

}
