<?php

namespace App\Http\Controllers;

use App\Contracts\SacreContact\UpdatesSacreContact;
use App\Models\Sacre;
use App\Models\SacreContact;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Contracts\SacreContact\AddsSacreContact;


class SacreContactController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sacre $sacre
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Sacre $sacre)
    {
        //
        app(AddsSacreContact::class)->add($sacre, $request->all());

        Return back(303);
    }


    /**
     * Update the given contacts details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\Models\Sacre $sacre
     * @param  int  $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Sacre $sacre, SacreContact $contact)
    {
        //
        app(UpdatesSacreContact::class)->update($contact, $request->all());

        return back(303);
    }

}
