<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignEmail extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'campaign_group_id',
        'name',
        'email'
    ];

    /**
     * Get the campaign group it belongs to.
     */
    public function group()
    {
        return $this->belongsTo(CampaignGroup::class, 'campaign_group_id');
    }

    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\RegionResource($this);
    }
}
