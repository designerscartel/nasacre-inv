<?php

namespace App\Http\Controllers;

use App\Contracts\SacreContact\UpdatesSacreContact;
use App\Contracts\SacreContact\AddsSacreContact;
use App\Actions\SacreContact\DeleteSacreContact;
use App\Models\Sacre;
use App\Models\SacreContact;
use Illuminate\Http\Request;
use Inertia\Inertia;


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

        return back(303);
    }


    /**
     * Update the given contacts details.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sacre $sacre
     * @param \App\Models\SacreContact $contact
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Sacre $sacre, SacreContact $contact)
    {
        //
        app(UpdatesSacreContact::class)->update($contact, $request->all());

        return back(303);
    }

    /**
     * Delete the current contact.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sacre $sacre
     * @param \App\Models\SacreContact $contact
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Sacre $sacre, SacreContact $contact)
    {
        //
        app(DeleteSacreContact::class)->delete($contact);

        return back(303);
    }


}
