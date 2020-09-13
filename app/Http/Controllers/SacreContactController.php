<?php

namespace App\Http\Controllers;

use App\Models\Sacre;
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

}
