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
        'message',
        'batch',
    ];

    protected $casts = [
        'batch' => 'integer',
    ];

    /**
     * Get the campaign group it belongs to.
     */
    public function group()
    {
        return $this->belongsTo(CampaignGroup::class, 'campaign_group_id');
    }

    public function deliveries()
    {
        return $this->hasMany(CampaignDelivery::class);
    }

    public function latestDeliverySummary()
    {
        $latestBatch = max((int) $this->batch, (int) $this->deliveries->max('batch'));
        $deliveries = $this->deliveries->where('batch', $latestBatch)->values();

        if ($deliveries->isEmpty()) {
            return null;
        }

        return [
            'batch' => $latestBatch,
            'total' => $deliveries->count(),
            'queued' => $deliveries->where('delivery_status', 'queued')->count(),
            'sent' => $deliveries->where('delivery_status', 'sent')->count(),
            'failed' => $deliveries->where('delivery_status', 'failed')->count(),
            'recipients' => $deliveries->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'name' => $delivery->recipient_name,
                    'email' => $delivery->recipient_email,
                    'status' => $delivery->delivery_status,
                    'failure_message' => $delivery->failure_message,
                ];
            })->values(),
        ];
    }

    /**
     * Return a Resource
     */
    public function toResource()
    {
        return new \App\Http\Resources\CampaignResource($this);
    }
}
