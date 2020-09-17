<?php

namespace App\Http\Controllers;

use App\Actions\Campaign\CreateCampaignInformation;
use App\Contracts\Campaign\UpdatesCampaignInformation;
use App\Models\Campaign;
use App\Http\Resources\CampaignResource;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Campaign $campaign)
    {
        //
        return Inertia::render('Campaigns/Dashboard', [
            'campaigns' => CampaignResource::collection($campaign::all())
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $campaign = app(CreateCampaignInformation::class)->create($request->all());

        return back(303);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Campaign $campaign
     * @return \Illuminate\Http\Response
     */
    public function show(Campaign $campaign)
    {
        //
        return Inertia::render('Campaigns/Show', [
            'Campaign' => $campaign->toResource(),
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Campaign $campaign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Campaign $campaign)
    {
        app(UpdatesCampaignInformation::class)->update($campaign, $request->all());

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Campaign $campaign
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campaigns $campaign)
    {
        //
    }
}
