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

    public function deliveryBatchHistory()
    {
        $allDeliveries = $this->deliveries;

        return $allDeliveries
            ->groupBy('batch')
            ->sortKeysDesc()
            ->map(function ($deliveries, $batch) use ($allDeliveries) {
                $newerRecipientEmails = $allDeliveries
                    ->where('batch', '>', (int) $batch)
                    ->pluck('recipient_email');
                $retryableFailed = $deliveries
                    ->where('delivery_status', 'failed')
                    ->whereNotIn('recipient_email', $newerRecipientEmails)
                    ->count();

                return [
                    'batch' => (int) $batch,
                    'total' => $deliveries->count(),
                    'queued' => $deliveries->where('delivery_status', 'queued')->count(),
                    'sent' => $deliveries->where('delivery_status', 'sent')->count(),
                    'failed' => $deliveries->where('delivery_status', 'failed')->count(),
                    'retryable_failed' => $retryableFailed,
                    'started_at' => optional($deliveries->min('queued_at') ?: $deliveries->min('created_at'))->toIso8601String(),
                    'recipients' => $deliveries->map(function ($delivery) {
                        return [
                            'id' => $delivery->id,
                            'name' => $delivery->recipient_name,
                            'email' => $delivery->recipient_email,
                            'status' => $delivery->delivery_status,
                            'queued_at' => optional($delivery->queued_at)->toIso8601String(),
                            'sent_at' => optional($delivery->sent_at)->toIso8601String(),
                            'failed_at' => optional($delivery->failed_at)->toIso8601String(),
                            'failure_message' => $delivery->failure_message,
                        ];
                    })->values(),
                ];
            })->values();
    }

    /**
     * Return a Resource
     */
    public function toResource(?string $resourceClass = null): \Illuminate\Http\Resources\Json\JsonResource
    {
        return new \App\Http\Resources\CampaignResource($this);
    }
}
