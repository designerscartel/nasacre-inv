<?php

namespace App\Contracts\CampaignEmail;

interface CreatesCampaignEmailInformation
{
    /**
     * Validate and update the given campaign's information.
     *
     * @param  array  $input
     * @param object $campaignGroup
     * @return void
     */
    public function create(array $input, object $campaignGroup);
}