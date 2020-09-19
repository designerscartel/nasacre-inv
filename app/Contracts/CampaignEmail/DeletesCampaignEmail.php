<?php

namespace App\Contracts\CampaignEmail;


interface DeletesCampaignEmail
{
    /**
     * Delete the given email.
     *
     * @param  mixed  $emailCamapign
     * @return void
     */
    public function delete($emailCamapign);
}