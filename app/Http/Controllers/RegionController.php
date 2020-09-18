<?php

namespace App\Http\Controllers;

use App\Actions\Region\CreateRegionInformation;
use App\Contracts\Region\UpdatesRegionInformation;
use App\Models\Region;
use App\Http\Resources\RegionResource;

use Illuminate\Http\Request;
use Inertia\Inertia;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Region $region)
    {
        //
        return Inertia::render('Regions/Dashboard', [
            'regions' => RegionResource::collection($region::all())
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
        return Inertia::render('Regions/Create', [
            'region' => null,
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
        app(CreateRegionInformation::class)->create($request->all());

        return back(303);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Region $region
     * @return \Illuminate\Http\Response
     */
    public function show(Region $region)
    {
        //
        return Inertia::render('Regions/Show', [
            'region' => $region->toResource(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Region $region
     * @return \Illuminate\Http\Response
     */
    public function edit(Region $region)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Region $region
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Region $region)
    {
        app(UpdatesRegionInformation::class)->update($region, $request->all());

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Region $region
     * @return \Illuminate\Http\Response
     */
    public function destroy(Regions $region)
    {
        //
    }
}
