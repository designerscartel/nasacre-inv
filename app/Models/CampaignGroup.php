<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignGroup extends Model
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

    /**
     * Get all of the emails.
     */
    public function emails()
    {
        return $this->hasMany(CampaignEmail::class);
    }

    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\CampaignGroupResource($this);
    }

    /**
     * Boot Function
     */
    public static function boot()
    {
        parent::boot();
        self::deleting(function ($campaignGroup) { // before delete() method call this
            $campaignGroup->emails()->each(function ($email) {
                $email->delete(); // <-- direct deletion
            });
        });
    }
}
