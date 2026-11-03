<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignDelivery extends Model
{
    protected $fillable = [
        'campaign_id',
        'batch',
        'delivery_status',
        'recipient_name',
        'recipient_email',
        'queued_at',
        'sent_at',
        'failed_at',
        'failure_message',
    ];

    protected $casts = [
        'campaign_id' => 'integer',
        'batch' => 'integer',
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
