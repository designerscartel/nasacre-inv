<?php

namespace App\Actions\CampaignEmail;

use App\Contracts\CampaignEmail\DeletesCampaignEmail;

class DeleteCampaignEmail implements DeletesCampaignEmail
{
    /**
     * Delete the given email.
     *
     * @param  mixed  $campaignEmail
     * @return void
     */
    public function delete($campaignEmail)
    {
        $campaignEmail->delete();
    }
}