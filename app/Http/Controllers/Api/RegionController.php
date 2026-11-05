<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Http\Resources\RegionResource;
use App\Http\Resources\PlaceOfInterestResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): AnonymousResourceCollection
    {
        $regions = Region::query()
            ->withCount([
                'places' => fn ($query) => $query
                    ->where('do_not_list', false)
                    ->where('is_place_of_interest', true),
            ])
            ->orderBy('title')
            ->get();

        return RegionResource::collection($regions);
    }

    public function places(Region $region): AnonymousResourceCollection
    {
        $places = $region->places()
            ->where('do_not_list', false)
            ->where('is_place_of_interest', true)
            ->with(['religion:id,name', 'regions:id,title'])
            ->orderBy('name')
            ->get();

        return PlaceOfInterestResource::collection($places);
    }
}
