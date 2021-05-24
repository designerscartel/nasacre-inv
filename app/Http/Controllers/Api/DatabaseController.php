<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sacre;
use App\Http\Resources\ApiSacreResource;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Sacre $sacre)
    {
        //
        return ApiSacreResource::collection($sacre::all());
    }


    public function show($term)
    {
        //
        $results = Sacre::search($term)->get();
        return ApiSacreResource::collection($results);
    }


}
