<?php

namespace App\Actions\Campaign;

use App\Models\Campaign;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Campaign\CreatesCampaignInformation;

class CreateCampaignInformation implements CreatesCampaignInformation
{
    /**
     * Validate and Create the given campaign's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'campaign_group_id' => ['required'],
            'email' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ])->validateWithBag('createCampaignInformation');

        $campaign = new Campaign();

        return $campaign->create([
            'campaign_group_id' => $input['campaign_group_id'],
            'email' => $input['email'],
            'subject' => $input['subject'],
            'url' => $input['url'],
            'message' => $input['message'],
        ]);

    }
}
