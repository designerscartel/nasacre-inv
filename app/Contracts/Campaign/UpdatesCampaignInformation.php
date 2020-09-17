<?php

namespace App\Contracts\Campaign;

interface UpdatesCampaignInformation
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