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
        $publicPlace = PlaceOfInterest::create(['name' => 'Public Centre']);
        $privatePlace = PlaceOfInterest::create(['name' => 'Private Centre', 'do_not_list' => true]);
        $region->places()->attach([$publicPlace->id, $privatePlace->id]);

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
            'religion_id' => $religion->id,
            'personal_email' => 'private@example.com',
            'notes' => 'Internal notes',
        ]);
        $privatePlace = PlaceOfInterest::create(['name' => 'Hidden Centre', 'do_not_list' => true]);
        $walesPlace = PlaceOfInterest::create(['name' => 'Wales Centre']);
        $london->places()->attach([$londonPlace->id, $privatePlace->id]);
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
