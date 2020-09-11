<?php

namespace App\Http\Controllers;

use App\Models\Sacre;
use App\Http\Resources\SacreResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashbordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Sacre $sacre)
    {
        //
        return Inertia::render('Dashboard', [
            'sacres' => SacreResource::collection($sacre::all())
        ]);
    }


}
