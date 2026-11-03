<?php

namespace App\Actions\Campaign;

use App\Jobs\SendCampaign;
use App\Models\CampaignDelivery;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RetryFailedCampaignDeliveries
{
    public function retry($campaign, array $input)
    {
        Validator::make($input, [
            'confirm_retry' => ['accepted'],
        ], [
            'confirm_retry.accepted' => 'You must confirm that failed campaign emails should be retried.',
        ])->validateWithBag('retryFailedCampaignDeliveries');

        $deliveries = DB::transaction(function () use ($campaign) {
            $lockedCampaign = $campaign->newQuery()->lockForUpdate()->findOrFail($campaign->id);
            $failedDeliveries = CampaignDelivery::where('campaign_id', $lockedCampaign->id)
                ->where('batch', $lockedCampaign->batch)
                ->where('delivery_status', 'failed')
                ->get();

            if ($failedDeliveries->isEmpty()) {
                return collect();
            }

            $lockedCampaign->batch = ((int) $lockedCampaign->batch) + 1;
            $lockedCampaign->save();

            return $failedDeliveries->map(function ($failedDelivery) use ($lockedCampaign) {
                return CampaignDelivery::create([
                    'campaign_id' => $lockedCampaign->id,
                    'batch' => $lockedCampaign->batch,
                    'delivery_status' => 'queued',
                    'recipient_name' => $failedDelivery->recipient_name,
                    'recipient_email' => $failedDelivery->recipient_email,
                    'queued_at' => now(),
                ]);
            });
        });

        $deliveries->each(function ($delivery) use ($campaign) {
            SendCampaign::dispatch([
                'campaign' => $campaign,
                'recipient_name' => $delivery->recipient_name,
                'recipient_email' => $delivery->recipient_email,
                'campaign_delivery_id' => $delivery->id,
            ]);
        });
    }
}
