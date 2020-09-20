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

        $details = ['email' => 'recipient@example.com'];
        SendCampaign::dispatch($details);


    }
}
