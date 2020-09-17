<?php

namespace App\Contracts\Campaign;

interface CreatesCampaignInformation
{
    /**
     * Validate and update the given campaign's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input);
}