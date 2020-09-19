<?php

namespace App\Actions\CampaignGroup;

use App\Contracts\CampaignGroup\DeletesCampaignGroup;

class DeleteCampaignGroup implements DeletesCampaignGroup
{
    /**
     * Delete the given group.
     *
     * @param  mixed  $campaignGroup
     * @return void
     */
    public function delete($campaignGroup)
    {
        $campaignGroup->delete();
    }
}