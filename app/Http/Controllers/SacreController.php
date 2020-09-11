<?php

namespace App\Http\Controllers;

use App\Contracts\Sacre\UpdatesSacreInformation;
use App\Models\Sacre;
use App\Models\Region;
use Cassandra\Type\Scalar;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SacreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return Inertia::render('Sacres/Create', [
            'sacre' => null,
            'regions' => Region::all()
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
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Sacre $sacre
     * @return \Illuminate\Http\Response
     */
    public function show(Sacre $sacre)
    {
        //
        return Inertia::render('Sacres/Show', [
            'sacre' => $sacre->toResource(),
            'regions' => Region::all()
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Sacre $sacre
     * @return \Illuminate\Http\Response
     */
    public function edit(Sacre $sacre)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sacre $sacre
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sacre $sacre)
    {
        app(UpdatesSacreInformation::class)->update($sacre, $request->all());

        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Sacre $sacre
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sacres $sacre)
    {
        //
    }
}
