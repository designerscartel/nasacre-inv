<?php

namespace App\Http\Controllers;

use App\Models\Sacre;
use App\Http\Resources\SacreDirectoryResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashbordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $sacres = Sacre::query()
            ->with('region')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('short_code', 'like', '%'.$search.'%')
                        ->orWhere('code', 'like', '%'.$search.'%')
                        ->orWhereHas('region', function ($query) use ($search) {
                            $query->where('title', 'like', '%'.$search.'%');
                        });
                });
            })
            ->orderBy('title')
            ->paginate(50)
            ->appends(['search' => $search]);

        return Inertia::render('Dashboard', [
            'sacres' => SacreDirectoryResource::collection($sacres),
            'filters' => ['search' => $search],
            'summary' => [
                'total' => Sacre::count(),
                'members' => Sacre::where('member', true)->count(),
                'non_members' => Sacre::where(function ($query) {
                    $query->where('member', false)->orWhereNull('member');
                })->count(),
            ],
        ]);
    }


}
