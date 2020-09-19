<?php

namespace App\Http\Controllers;

use App\Actions\CampaignEmail\CreateCampaignEmailInformation;
use App\Contracts\CampaignEmail\DeletesCampaignEmail;
use App\Contracts\CampaignEmail\UpdatesCampaignEmailInformation;
use App\Models\CampaignGroup;
use App\Models\CampaignEmail;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignEmailController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CampaignGroup $campaignGroup
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, CampaignGroup $campaignGroup)
    {
        //
        app(CreateCampaignEmailInformation::class)->create($request->all(), $campaignGroup);

        return back(303);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CampaignGroup $campaignGroup
     * @param \App\Models\CampaignEmail $campaignEmail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CampaignGroup $campaignGroup, CampaignEmail $campaignEmail)
    {
        app(UpdatesCampaignEmailInformation::class)->update($campaignEmail, $request->all());

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\CampaignEmail $campaignEmail
     * @return \Illuminate\Http\Response
     */
    public function destroy(CampaignGroup $campaignGroup, CampaignEmail $campaignEmail)
    {
        //
        app(DeletesCampaignEmail::class)->delete($campaignEmail);

        return back(303);
    }
}
