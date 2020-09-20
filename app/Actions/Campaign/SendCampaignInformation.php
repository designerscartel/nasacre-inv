<?php

namespace App\Actions\Campaign;

use App\Contracts\Campaign\SendsCampaignInformation;
use App\Jobs\SendCampaign;
use App\Models\Campaign;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        ])->validateWithBag('sendCampaignInformation');

        if(!empty($campaign->group->emails)){

            foreach($campaign->group->emails as $email) {

                $details = [
                    'campaign' => $campaign,
                    'email' => $email
                ];

                SendCampaign::dispatch($details);
            }
        }

    }
}
