<?php

namespace App\Contracts\CampaignGroup;

interface CreatesCampaignGroupInformation
{
    /**
     * Validate and update the given campaign's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input);
}