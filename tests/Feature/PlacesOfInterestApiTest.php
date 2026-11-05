<?php

namespace Tests\Feature;

use App\Models\PlaceOfInterest;
use App\Models\Region;
use App\Models\Religion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlacesOfInterestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_regions_include_public_place_counts(): void
    {
        $region = Region::create(['title' => 'London']);
        $publicPlace = PlaceOfInterest::create(['name' => 'Public Centre', 'is_place_of_interest' => true]);
        $privatePlace = PlaceOfInterest::create(['name' => 'Private Centre', 'is_place_of_interest' => true, 'do_not_list' => true]);
        $speaker = PlaceOfInterest::create(['name' => 'School Speaker', 'is_school_speaker' => true]);
        $region->places()->attach([$publicPlace->id, $privatePlace->id, $speaker->id]);

        $this->getJson(route('api.region'))
            ->assertOk()
            ->assertJsonPath('data.0.id', $region->id)
            ->assertJsonPath('data.0.title', 'London')
            ->assertJsonPath('data.0.places_count', 1);
    }

    public function test_places_can_be_fetched_for_a_chosen_region(): void
    {
        $london = Region::create(['title' => 'London']);
        $wales = Region::create(['title' => 'Wales']);
        $religion = Religion::create(['name' => 'Humanism']);
        $londonPlace = PlaceOfInterest::create([
            'name' => 'London Centre',
            'is_place_of_interest' => true,
            'religion_id' => $religion->id,
            'personal_email' => 'private@example.com',
            'notes' => 'Internal notes',
        ]);
        $privatePlace = PlaceOfInterest::create(['name' => 'Hidden Centre', 'is_place_of_interest' => true, 'do_not_list' => true]);
        $speaker = PlaceOfInterest::create(['name' => 'London Speaker', 'is_school_speaker' => true]);
        $walesPlace = PlaceOfInterest::create(['name' => 'Wales Centre', 'is_place_of_interest' => true]);
        $london->places()->attach([$londonPlace->id, $privatePlace->id, $speaker->id]);
        $wales->places()->attach($walesPlace);

        $response = $this->getJson(route('api.region.places', $london));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'London Centre')
            ->assertJsonPath('data.0.religion', 'Humanism')
            ->assertJsonPath('data.0.regions.0.title', 'London')
            ->assertJsonMissing(['personal_email' => 'private@example.com'])
            ->assertJsonMissing(['notes' => 'Internal notes'])
            ->assertJsonMissing(['name' => 'Hidden Centre'])
            ->assertJsonMissing(['name' => 'Wales Centre']);
    }

    public function test_re_hub_region_endpoints_list_only_regions_with_the_requested_entry_type(): void
    {
        $london = Region::create(['title' => 'London']);
        $wales = Region::create(['title' => 'Wales']);
        $empty = Region::create(['title' => 'North East']);
        $place = PlaceOfInterest::create(['name' => 'London Centre', 'is_place_of_interest' => true]);
        $speaker = PlaceOfInterest::create(['name' => 'Welsh Speaker', 'is_school_speaker' => true]);
        $both = PlaceOfInterest::create([
            'name' => 'Shared Entry',
            'is_place_of_interest' => true,
            'is_school_speaker' => true,
        ]);
        $london->places()->attach([$place->id, $both->id]);
        $wales->places()->attach([$speaker->id, $both->id]);

        $this->getJson(route('api.re-hubs.regions.places-of-interest'))
            ->assertOk()
            ->assertJsonPath('type', 'place_of_interest')
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.title', 'London')
            ->assertJsonPath('data.0.entries_count', 2)
            ->assertJsonMissing(['title' => $empty->title]);

        $this->getJson(route('api.re-hubs.regions.school-speakers', ['search' => 'Wal']))
            ->assertOk()
            ->assertJsonPath('type', 'school_speaker')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Wales')
            ->assertJsonPath('data.0.entries_count', 2);
    }

    public function test_re_hub_entry_endpoints_are_searchable_and_filterable(): void
    {
        $london = Region::create(['title' => 'London']);
        $wales = Region::create(['title' => 'Wales']);
        $humanism = Religion::create(['name' => 'Humanism']);
        $christianity = Religion::create(['name' => 'Christianity']);
        $speaker = PlaceOfInterest::create([
            'name' => 'Alex Morgan',
            'is_school_speaker' => true,
            'religion_id' => $humanism->id,
            'organisation_email' => 'alex@example.com',
        ]);
        $otherSpeaker = PlaceOfInterest::create([
            'name' => 'Chris Jones',
            'is_school_speaker' => true,
            'religion_id' => $christianity->id,
        ]);
        $place = PlaceOfInterest::create([
            'name' => 'London Faith Centre',
            'is_place_of_interest' => true,
            'religion_id' => $humanism->id,
        ]);
        $hidden = PlaceOfInterest::create([
            'name' => 'Hidden Speaker',
            'is_school_speaker' => true,
            'do_not_list' => true,
        ]);
        $london->places()->attach([$speaker->id, $place->id, $hidden->id]);
        $wales->places()->attach($otherSpeaker);

        $this->getJson(route('api.re-hubs.school-speakers', [
            'search' => 'alex@example.com',
            'region' => $london->id,
            'religion' => $humanism->id,
        ]))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Alex Morgan')
            ->assertJsonMissing(['name' => 'Hidden Speaker']);

        $this->getJson(route('api.re-hubs.places-of-interest', ['search' => 'Humanism']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'London Faith Centre');
    }

    public function test_re_hub_filters_are_validated(): void
    {
        $this->getJson(route('api.re-hubs.school-speakers', ['region' => 999]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('region');

        $this->getJson(route('api.re-hubs.regions.places-of-interest', ['religion' => 999]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('religion');
    }
}
