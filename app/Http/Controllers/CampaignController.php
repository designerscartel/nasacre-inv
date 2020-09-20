<?php

namespace App\Http\Controllers;

use App\Actions\Campaign\CreateCampaignInformation;
use App\Contracts\Campaign\SendsCampaignInformation;
use App\Contracts\Campaign\UpdatesCampaignInformation;
use App\Models\Campaign;
use App\Models\CampaignGroup;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return Inertia::render('Campaigns/Create', [
            'groups' => \App\Http\Resources\CampaignGroupResource::collection(CampaignGroup::all()),
            'campaign' => null,
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

        return redirect()->route('campaigns.show', ['campaign' => $campaign->id]);
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
            'groups' => \App\Http\Resources\CampaignGroupResource::collection(CampaignGroup::all()),
            'campaign' => $campaign->toResource(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Campaign $campaign
     * @return \Illuminate\Http\Response
     */
    public function edit(Campaign $campaign)
    {
        //
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
     * Send the specified resource in storage
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Campaign $campaign
     * @return \Illuminate\Http\Response
     */
    public function Send(Request $request, Campaign $campaign)
    {
        //
        app(SendsCampaignInformation::class)->send($campaign, $request->all());

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
