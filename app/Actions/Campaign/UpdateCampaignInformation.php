<?php

namespace App\Actions\Campaign;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Campaign\UpdatesCampaignInformation;

class UpdateCampaignInformation implements UpdatesCampaignInformation
{
    /**
     * Validate and update the given Campaign's information.
     *
     * @param  mixed  $Campaign
     * @param  array  $input
     * @return void
     */
    public function update($Campaign, array $input)
    {
        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateCampaignInformation');

        $Campaign->fill([
            'title' => $input['title'],
        ])->save();
    }
}
