<?php

namespace App\Contracts\CampaignGroup;


interface DeletesCampaignGroup
{
    /**
     * Delete the given group.
     *
     * @param  mixed  $campaignGroup
     * @return void
     */
    public function delete($campaignGroup);
}