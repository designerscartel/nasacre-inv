<?php

namespace App\Contracts\Campaign;

interface SendsCampaignInformation
{
    /**
     * Validate and sends the given campaign's information.
     *
     * @param  mixed  $campaign
     * @param  array  $input
     * @return void
     */
    public function send($campaign, array $input);
}