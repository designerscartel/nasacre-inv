<?php

namespace App\Actions\CampaignEmail;

use App\Models\CampaignEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\CampaignEmail\CreatesCampaignEmailInformation;

class CreateCampaignEmailInformation implements CreatesCampaignEmailInformation
{
    /**
     * Validate and Create the given Campaign's information.
     *
     * @param  array  $input
     * @param object $campaignGroup
     * @return void
     */
    public function create(array $input, object $campaignGroup)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
        ])->validateWithBag('addEmailForm');

        $campaignEmail = new CampaignEmail();

        return $campaignEmail->create([
            'campaign_group_id' => $campaignGroup->id,
            'name' => $input['name'],
            'email' => $input['email'],
        ]);

    }
}
