<?php

namespace App\Actions\Campaign;

use App\Models\Campaign;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Campaign\CreatesCampaignInformation;

class CreateCampaignInformation implements CreatesCampaignInformation
{
    /**
     * Validate and Create the given Campaign's information.
     *
     * @param  array  $input
     * @return void
     */
    public function Create(array $input)
    {
        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createCampaignInformation');

        $Campaign = new Campaign();

        return $Campaign->create([
            'title' => $input['title'],
        ]);

    }
}
