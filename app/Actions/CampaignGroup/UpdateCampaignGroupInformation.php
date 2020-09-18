<?php

namespace App\Actions\CampaignGroup;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\CampaignGroup\UpdatesCampaignGroupInformation;

class UpdateCampaignGroupInformation implements UpdatesCampaignGroupInformation
{
    /**
     * Validate and update the given Campaign's information.
     *
     * @param  mixed  $campaignGroup
     * @param  array  $input
     * @return void
     */
    public function update($campaignGroup, array $input)
    {
        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateCampaignGroupInformation');

        $campaignGroup->fill([
            'title' => $input['title'],
        ])->save();
    }
}
