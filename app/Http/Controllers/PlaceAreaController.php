<?php

namespace App\Http\Controllers;

use App\Models\PlaceArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PlaceAreaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('PlaceAreas/Index', [
            'areas' => PlaceArea::query()
                ->withCount('places')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        PlaceArea::create($request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:place_areas,name'],
        ]));

        return back(303)->with('success', 'Area created.');
    }

    public function update(Request $request, PlaceArea $placeArea): RedirectResponse
    {
        $placeArea->update($request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('place_areas', 'name')->ignore($placeArea)],
        ]));

        return back(303)->with('success', 'Area updated.');
    }

    public function destroy(PlaceArea $placeArea): RedirectResponse
    {
        if ($placeArea->places()->exists()) {
            return back(303)->withErrors([
                'area' => 'Move or delete this area’s places before deleting it.',
            ]);
        }

        $placeArea->delete();

        return back(303)->with('success', 'Area deleted.');
    }
}
