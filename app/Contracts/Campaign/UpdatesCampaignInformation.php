<?php

namespace App\Contracts\Campaign;

interface UpdatesCampaignInformation
{
    /**
     * Validate and update the given campaign's information.
     *
     * @param  mixed  $campaign
     * @param  array  $input
     * @return void
     */
    public function update($campaign, array $input);
}