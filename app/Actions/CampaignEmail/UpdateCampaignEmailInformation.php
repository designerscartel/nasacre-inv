<?php

namespace App\Actions\CampaignEmail;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\CampaignEmail\UpdatesCampaignEmailInformation;

class UpdateCampaignEmailInformation implements UpdatesCampaignEmailInformation
{
    /**
     * Validate and update the given Campaign's information.
     *
     * @param  mixed  $campaignEmail
     * @param  array  $input
     * @return void
     */
    public function update($campaignEmail, array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateEmailForm');

        $campaignEmail->fill([
            'name' => $input['name'],
            'email' => $input['email'],
        ])->save();
    }
}
