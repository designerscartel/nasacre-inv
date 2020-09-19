<?php

namespace App\Http\Controllers;

use App\Actions\CampaignGroup\CreateCampaignGroupInformation;
use App\Actions\CampaignGroup\DeleteCampaignGroup;
use App\Contracts\CampaignGroup\UpdatesCampaignGroupInformation;
use App\Models\CampaignGroup;
use App\Http\Resources\CampaignGroupResource;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CampaignGroup $campaignGroup)
    {
        //
        return Inertia::render('CampaignGroups/Dashboard', [
            'groups' => CampaignGroupResource::collection($campaignGroup::all())
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
        $campaign = app(CreateCampaignGroupInformation::class)->create($request->all());

        return back(303);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\CampaignGroup $campaignGroup
     * @return \Illuminate\Http\Response
     */
    public function show(CampaignGroup $campaignGroup)
    {
        //
        return Inertia::render('CampaignGroups/Show', [
            'group' => $campaignGroup->toResource(),
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CampaignGroup $campaignGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CampaignGroup $campaignGroup)
    {
        app(UpdatesCampaignGroupInformation::class)->update($campaignGroup, $request->all());

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\CampaignGroup $campaignGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(CampaignGroup $campaignGroup)
    {
        //
        app(DeleteCampaignGroup::class)->delete($campaignGroup);

        return back(303);
    }
}
