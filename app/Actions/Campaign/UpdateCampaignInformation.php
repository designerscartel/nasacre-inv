<?php

namespace App\Actions\Campaign;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Campaign\UpdatesCampaignInformation;

class UpdateCampaignInformation implements UpdatesCampaignInformation
{
    /**
     * Validate and update the given campaign's information.
     *
     * @param  mixed  $campaign
     * @param  array  $input
     * @return void
     */
    public function update($campaign, array $input)
    {
        Validator::make($input, [
            'campaign_group_id' => ['required'],
            'email' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateCampaignInformation');

        $campaign->fill([
            'campaign_group_id' => $input['campaign_group_id'],
            'email' => $input['email'],
            'subject' => $input['subject'],
            'url' => $input['url'],
            'message' => $input['message'],
        ])->save();
    }
}
