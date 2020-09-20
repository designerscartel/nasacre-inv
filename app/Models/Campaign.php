<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'campaign_group_id',
        'email',
        'subject' ,
        'url',
        'message'
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
        return new \App\Http\Resources\CampaignResource($this);
    }
}
