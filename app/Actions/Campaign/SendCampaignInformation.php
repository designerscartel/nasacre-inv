<?php

namespace App\Actions\Campaign;

use App\Contracts\Campaign\SendsCampaignInformation;
use App\Jobs\SendCampaign;
use App\Models\CampaignDelivery;
use App\Support\CampaignSendPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SendCampaignInformation implements SendsCampaignInformation
{
    /**
     * Validate and Sends the given campaign's information.
     *
     * @param  mixed  $campaign
     * @param  array  $input
     * @return void
     */
    public function send($campaign, array $input)
    {

        Validator::make($input, [
            'send' => ['required'],
            'confirm_resend' => ['nullable'],
        ])->validateWithBag('sendCampaignInformation');

        $recipients = (new CampaignSendPlan())
            ->forCampaign($campaign)['recipients'];

        if ($recipients->isEmpty()) {
            return;
        }

        $batch = DB::transaction(function () use ($campaign, $input) {
            $lockedCampaign = $campaign->newQuery()->lockForUpdate()->findOrFail($campaign->id);
            $hasBeenSent = (int) $lockedCampaign->batch > 0
                || CampaignDelivery::where('campaign_id', $lockedCampaign->id)->exists();

            Validator::make($input, [
                'confirm_resend' => $hasBeenSent ? ['accepted'] : ['nullable'],
            ], [
                'confirm_resend.accepted' => 'You must confirm that this campaign is being resent.',
            ])->validateWithBag('sendCampaignInformation');

            $lockedCampaign->batch = ((int) $lockedCampaign->batch) + 1;
            $lockedCampaign->save();

            return $lockedCampaign->batch;
        });

        $recipients->each(function ($email) use ($campaign, $batch) {
            $delivery = CampaignDelivery::create([
                'campaign_id' => $campaign->id,
                'batch' => $batch,
                'delivery_status' => 'queued',
                'recipient_name' => $email->name,
                'recipient_email' => $email->email,
                'queued_at' => now(),
            ]);

            SendCampaign::dispatch([
                'campaign' => $campaign,
                'recipient_name' => $delivery->recipient_name,
                'recipient_email' => $delivery->recipient_email,
                'campaign_delivery_id' => $delivery->id,
            ]);
        });

    }
}
