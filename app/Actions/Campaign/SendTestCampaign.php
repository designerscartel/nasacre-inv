<?php

namespace App\Actions\Campaign;

use App\Mail\CampaignForQueuing;
use App\Models\CampaignEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SendTestCampaign
{
    public function send($campaign, array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ])->validateWithBag('sendTestCampaign');

        $recipient = new CampaignEmail([
            'name' => $input['name'],
            'email' => $input['email'],
        ]);

        Mail::to($recipient->email)->send(
            new CampaignForQueuing($campaign, $recipient, true)
        );
    }
}
