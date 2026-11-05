<?php

namespace App\Http\Controllers;

use App\Models\PlaceOfInterest;
use App\Models\Region;
use App\Models\Religion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PlaceOfInterestController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'integer', 'exists:regions,id'],
            'type' => ['nullable', 'in:poi,speaker'],
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
            ->when($filters['type'] ?? null, function ($query, $type) {
                $query->where($type === 'poi' ? 'is_place_of_interest' : 'is_school_speaker', true);
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
                'type' => $filters['type'] ?? '',
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

        return back(303)->with('success', 'RE Hub entry created.');
    }

    public function update(Request $request, PlaceOfInterest $place): RedirectResponse
    {
        $data = $this->validatePlace($request);
        $regionIds = $data['region_ids'];
        unset($data['region_ids']);

        $place->update($data);
        $place->regions()->sync($regionIds);

        return back(303)->with('success', 'RE Hub entry updated.');
    }

    public function destroy(PlaceOfInterest $place): RedirectResponse
    {
        $place->regions()->detach();
        $place->delete();

        return back(303)->with('success', 'RE Hub entry deleted.');
    }

    private function validatePlace(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_place_of_interest' => ['required', 'boolean'],
            'is_school_speaker' => ['required', 'boolean'],
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

        if (! $data['is_place_of_interest'] && ! $data['is_school_speaker']) {
            throw ValidationException::withMessages([
                'is_place_of_interest' => 'Select at least one entry type.',
            ]);
        }

        return $data;
    }
}
