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
}
