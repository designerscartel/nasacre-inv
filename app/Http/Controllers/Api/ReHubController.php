<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaceOfInterestResource;
use App\Http\Resources\ReHubRegionResource;
use App\Http\Resources\RegionResource;
use App\Models\PlaceOfInterest;
use App\Models\Region;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReHubController extends Controller
{
    private const EXCLUDED_REGION = 'Wales';

    public function placeRegions(Request $request): AnonymousResourceCollection
    {
        return $this->regions($request, 'is_place_of_interest', 'place_of_interest');
    }

    public function speakerRegions(Request $request): AnonymousResourceCollection
    {
        return $this->regions($request, 'is_school_speaker', 'school_speaker');
    }

    public function places(Request $request): AnonymousResourceCollection
    {
        return $this->entries($request, 'is_place_of_interest');
    }

    public function speakers(Request $request): AnonymousResourceCollection
    {
        return $this->entries($request, 'is_school_speaker');
    }

    public function legacyRegions(): AnonymousResourceCollection
    {
        $regions = Region::query()
            ->where('title', '!=', self::EXCLUDED_REGION)
            ->withCount([
                'places' => fn (Builder $query) => $this->publicEntries($query, 'is_place_of_interest'),
            ])
            ->orderBy('title')
            ->get();

        return RegionResource::collection($regions);
    }

    public function legacyRegionPlaces(Region $region): AnonymousResourceCollection
    {
        $places = $region->places()
            ->tap(fn (Builder $query) => $this->publicEntries($query, 'is_place_of_interest'))
            ->with(['religion:id,name', 'regions:id,title'])
            ->orderBy('name')
            ->get();

        return PlaceOfInterestResource::collection($places);
    }

    private function regions(Request $request, string $typeColumn, string $type): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'religion' => ['nullable', 'integer', 'exists:religions,id'],
        ]);

        $entryFilters = array_diff_key($filters, ['search' => true]);
        $matchingEntries = fn (Builder $query) => $this->filterEntries($query, $entryFilters, $typeColumn);

        $regions = Region::query()
            ->where('title', '!=', self::EXCLUDED_REGION)
            ->whereHas('places', $matchingEntries)
            ->withCount(['places as entries_count' => $matchingEntries])
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query
                ->where('title', 'like', "%{$search}%"))
            ->orderBy('title')
            ->get();

        return ReHubRegionResource::collection($regions)->additional(['type' => $type]);
    }

    private function entries(Request $request, string $typeColumn): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'integer', 'exists:regions,id'],
            'religion' => ['nullable', 'integer', 'exists:religions,id'],
        ]);

        $entries = $this->filterEntries(PlaceOfInterest::query(), $filters, $typeColumn)
            ->when($filters['region'] ?? null, fn (Builder $query, int $region) => $query
                ->whereHas('regions', fn (Builder $query) => $query->whereKey($region)))
            ->with(['religion:id,name', 'regions:id,title'])
            ->orderBy('name')
            ->get();

        return PlaceOfInterestResource::collection($entries);
    }

    private function filterEntries(Builder $query, array $filters, string $typeColumn): Builder
    {
        return $this->publicEntries($query, $typeColumn)
            ->when($filters['religion'] ?? null, fn (Builder $query, int $religion) => $query
                ->where('religion_id', $religion))
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%")
                        ->orWhere('organisation_email', 'like', "%{$search}%")
                        ->orWhereHas('religion', fn (Builder $query) => $query
                            ->where('name', 'like', "%{$search}%"));
                });
            });
    }

    private function publicEntries(Builder $query, string $typeColumn): Builder
    {
        return $query
            ->where('do_not_list', false)
            ->where($typeColumn, true)
            ->whereDoesntHave('regions', fn (Builder $query) => $query
                ->where('title', self::EXCLUDED_REGION));
    }
}
