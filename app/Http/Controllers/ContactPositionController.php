<?php

namespace App\Http\Controllers;

use App\Contracts\ContactPosition\CreatesContactPositionInformation;
use App\Contracts\ContactPosition\UpdatesContactPositionInformation;
use App\Models\ContactPosition;
use App\Http\Resources\ContactPositionResource;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ContactPosition $contactPosition)
    {
        //
        return Inertia::render('Positions/Dashboard', [
            'positions' => ContactPositionResource::collection($contactPosition::all())
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
        return Inertia::render('Positions/Create', [
            'position' => null,
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
        app(CreatesContactPositionInformation::class)->create($request->all());

        return back(303);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ContactPosition $contactPosition
     * @return \Illuminate\Http\Response
     */
    public function show(ContactPosition $contactPosition)
    {
        //
        return Inertia::render('Positions/Show', [
            'position' => $contactPosition->toResource(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ContactPosition $contactPosition
     * @return \Illuminate\Http\Response
     */
    public function edit(ContactPosition $contactPosition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ContactPosition $contactPosition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ContactPosition $contactPosition)
    {
        app(UpdatesContactPositionInformation::class)->update($contactPosition, $request->all());

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ContactPosition $contactPosition
     * @return \Illuminate\Http\Response
     */
    public function destroy(ContactPosition $contactPosition)
    {
        //
    }
}
