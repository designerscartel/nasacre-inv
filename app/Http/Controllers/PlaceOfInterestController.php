<?php

namespace App\Http\Controllers;

use App\Models\PlaceArea;
use App\Models\PlaceOfInterest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlaceOfInterestController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'area' => ['nullable', 'integer', 'exists:place_areas,id'],
        ]);

        $places = PlaceOfInterest::query()
            ->with('areas:id,name')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('religion', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%");
                });
            })
            ->when($filters['area'] ?? null, function ($query, $area) {
                $query->whereHas('areas', fn ($query) => $query->whereKey($area));
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Places/Index', [
            'places' => $places,
            'areas' => PlaceArea::query()->orderBy('name')->get(['id', 'name']),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'area' => $filters['area'] ?? '',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePlace($request);
        $areaIds = $data['area_ids'];
        unset($data['area_ids']);

        $place = PlaceOfInterest::create($data);
        $place->areas()->sync($areaIds);

        return back(303)->with('success', 'Place created.');
    }

    public function update(Request $request, PlaceOfInterest $place): RedirectResponse
    {
        $data = $this->validatePlace($request);
        $areaIds = $data['area_ids'];
        unset($data['area_ids']);

        $place->update($data);
        $place->areas()->sync($areaIds);

        return back(303)->with('success', 'Place updated.');
    }

    public function destroy(PlaceOfInterest $place): RedirectResponse
    {
        $place->areas()->detach();
        $place->delete();

        return back(303)->with('success', 'Place deleted.');
    }

    private function validatePlace(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'religion' => ['nullable', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'organisation_email' => ['nullable', 'email', 'max:255'],
            'personal_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'last_training' => ['nullable', 'string', 'max:255'],
            'charge_to_speak' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'do_not_list' => ['required', 'boolean'],
            'area_ids' => ['required', 'array', 'min:1'],
            'area_ids.*' => ['integer', 'exists:place_areas,id'],
        ]);
    }
}
