<?php

namespace App\Http\Controllers;

use App\Models\PlaceOfInterest;
use App\Models\Region;
use App\Models\Religion;
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
            'region' => ['nullable', 'integer', 'exists:regions,id'],
        ]);

        $places = PlaceOfInterest::query()
            ->with(['regions:id,title', 'religion:id,name'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('religion', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                        ->orWhere('contact_name', 'like', "%{$search}%");
                });
            })
            ->when($filters['region'] ?? null, function ($query, $region) {
                $query->whereHas('regions', fn ($query) => $query->whereKey($region));
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Places/Index', [
            'places' => $places,
            'regions' => Region::query()->orderBy('title')->get(['id', 'title']),
            'religions' => Religion::query()->orderBy('name')->get(['id', 'name']),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'region' => $filters['region'] ?? '',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePlace($request);
        $regionIds = $data['region_ids'];
        unset($data['region_ids']);

        $place = PlaceOfInterest::create($data);
        $place->regions()->sync($regionIds);

        return back(303)->with('success', 'Place created.');
    }

    public function update(Request $request, PlaceOfInterest $place): RedirectResponse
    {
        $data = $this->validatePlace($request);
        $regionIds = $data['region_ids'];
        unset($data['region_ids']);

        $place->update($data);
        $place->regions()->sync($regionIds);

        return back(303)->with('success', 'Place updated.');
    }

    public function destroy(PlaceOfInterest $place): RedirectResponse
    {
        $place->regions()->detach();
        $place->delete();

        return back(303)->with('success', 'Place deleted.');
    }

    private function validatePlace(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'religion_id' => ['nullable', 'integer', 'exists:religions,id'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'organisation_email' => ['nullable', 'email', 'max:255'],
            'personal_email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'last_training' => ['nullable', 'string', 'max:255'],
            'charge_to_speak' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'do_not_list' => ['required', 'boolean'],
            'region_ids' => ['required', 'array', 'min:1'],
            'region_ids.*' => ['integer', 'exists:regions,id'],
        ]);
    }
}
