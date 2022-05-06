<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sacre extends Model
{
    use HasFactory;
    use SoftDeletes;

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
     * Get all of the contacts.
     */
    public function contacts()
    {
        return $this->hasMany(SacreContact::class);
    }

    /**
     * Get all of the invoices.
     */
    public function invoices()
    {
        return $this->hasMany(SacreInvoice::class);
    }

    /**
     * Get all of the files.
     */
    public function files()
    {
        return $this->hasMany(SacreFile::class)->orderBy('year', 'DESC');
    }

    /**
     * Get all of the shared.
     */
    public function shared()
    {
        return $this->hasMany(SharedFile::class);
    }

    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\SacreResource($this);
    }


    /**
     * Boot Function
     */
    public static function boot()
    {
        parent::boot();
        self::deleting(function ($sacre) { // before delete() method call this
            $sacre->contacts()->each(function ($contact) {
                $contact->delete(); // <-- direct deletion
            });
        });
    }


    /**
     * Search Sacre
     */
    public function scopeSearch($query, $term)
    {
        $query->when($term ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%');
            });
        });
    }

}
