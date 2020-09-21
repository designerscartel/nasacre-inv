<?php

namespace App\Actions\CampaignGroup;

use App\Models\CampaignGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\CampaignGroup\CreatesCampaignGroupInformation;

class CreateCampaignGroupInformation implements CreatesCampaignGroupInformation
{
    /**
     * Validate and Create the given Campaign's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createCampaignGroupInformation');

        $campaignGroup = new CampaignGroup();

        return $campaignGroup->create([
            'title' => $input['title'],
        ]);

    }
}
