<?php

namespace App\Contracts\CampaignGroup;

interface UpdatesCampaignGroupInformation
{
    /**
     * Validate and update the given Campaign's information.
     *
     * @param  mixed  $Campaign
     * @param  array  $input
     * @return void
     */
    public function update($Campaign, array $input);
}