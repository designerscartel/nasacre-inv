<?php

namespace Tests\Feature;

use App\Models\PlaceOfInterest;
use App\Models\Region;
use App\Models\Religion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlacesOfInterestTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_import_creates_places_regions_and_multi_region_links(): void
    {
        $this->artisan('places:import')
            ->expectsOutput('Imported 41 places of interest.')
            ->assertSuccessful();

        $this->assertDatabaseCount('places_of_interest', 41);
        $this->assertDatabaseHas('regions', ['title' => 'London']);
        $this->assertDatabaseCount('religions', 7);
        $this->assertDatabaseHas('religions', ['name' => 'Humanism']);
        $this->assertTrue(PlaceOfInterest::query()->where('do_not_list', true)->exists());
        $this->assertTrue(PlaceOfInterest::query()->whereHas('regions', fn ($query) => $query->where('title', 'London'))->exists());
        $this->assertSame(4, PlaceOfInterest::query()->has('regions', '>', 1)->count());

        $regionCount = Region::count();
        $this->artisan('places:import')->assertSuccessful();
        $this->assertDatabaseCount('places_of_interest', 41);
        $this->assertSame($regionCount, Region::count());
    }

    public function test_authenticated_user_can_manage_places(): void
    {
        $this->actingAs($this->userWithTeam());
        $london = Region::create(['title' => 'London']);
        $southEast = Region::create(['title' => 'South East']);

        $this->post(route('places.store'), [
            'name' => 'Test Centre',
            'religion_id' => Religion::create(['name' => 'Humanism'])->id,
            'contact_name' => 'Alex Example',
            'organisation_email' => 'hello@example.com',
            'personal_email' => null,
            'phone' => '01234 567890',
            'website' => 'https://example.com',
            'last_training' => 'March 2026',
            'charge_to_speak' => 'No charge',
            'notes' => 'Test notes',
            'do_not_list' => false,
            'region_ids' => [$london->id, $southEast->id],
        ])->assertRedirect();

        $place = PlaceOfInterest::firstOrFail();
        $this->assertCount(2, $place->regions);

        $this->put(route('places.update', $place), [
            'name' => 'Updated Centre',
            'religion_id' => null,
            'contact_name' => null,
            'organisation_email' => null,
            'personal_email' => null,
            'phone' => null,
            'website' => null,
            'last_training' => null,
            'charge_to_speak' => null,
            'notes' => null,
            'do_not_list' => true,
            'region_ids' => [$london->id],
        ])->assertRedirect();

        $this->assertDatabaseHas('places_of_interest', [
            'id' => $place->id,
            'name' => 'Updated Centre',
            'do_not_list' => true,
        ]);
        $this->assertCount(1, $place->fresh()->regions);

        $this->delete(route('places.destroy', $place))->assertRedirect();
        $this->assertSoftDeleted($place);
        $this->assertDatabaseMissing('place_of_interest_region', ['place_of_interest_id' => $place->id]);
    }

    public function test_place_directory_can_be_filtered_by_region(): void
    {
        $this->actingAs($this->userWithTeam());
        $london = Region::create(['title' => 'London']);
        $wales = Region::create(['title' => 'Wales']);
        $londonPlace = PlaceOfInterest::create(['name' => 'London Centre']);
        $walesPlace = PlaceOfInterest::create(['name' => 'Wales Centre']);
        $londonPlace->regions()->attach($london);
        $walesPlace->regions()->attach($wales);

        $response = $this->get(route('places.index', ['region' => $london->id]), [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $response->assertJsonPath('props.filters.region', (string) $london->id);
        $response->assertJsonCount(1, 'props.places.data');
        $response->assertJsonPath('props.places.data.0.name', 'London Centre');
    }

    public function test_region_with_places_cannot_be_deleted(): void
    {
        $this->actingAs($this->userWithTeam());
        $region = Region::create(['title' => 'London']);
        $place = PlaceOfInterest::create(['name' => 'Test Centre']);
        $place->regions()->attach($region);

        $this->delete(route('regions.destroy', $region))->assertSessionHasErrors('region');
        $this->assertDatabaseHas('regions', ['id' => $region->id, 'deleted_at' => null]);

        $place->regions()->detach();
        $this->delete(route('regions.destroy', $region))->assertRedirect();
        $this->assertSoftDeleted($region);
    }

    public function test_authenticated_user_can_manage_religions(): void
    {
        $this->actingAs($this->userWithTeam());

        $this->post(route('religions.store'), ['name' => 'Humanism'])->assertRedirect();
        $religion = Religion::firstOrFail();

        $this->put(route('religions.update', $religion), ['name' => 'Humanist'])->assertRedirect();
        $this->assertDatabaseHas('religions', ['id' => $religion->id, 'name' => 'Humanist']);

        $place = PlaceOfInterest::create([
            'name' => 'Test Centre',
            'religion_id' => $religion->id,
        ]);
        $this->delete(route('religions.destroy', $religion))->assertSessionHasErrors('religion');

        $place->update(['religion_id' => null]);
        $this->delete(route('religions.destroy', $religion))->assertRedirect();
        $this->assertSoftDeleted($religion);
    }

    private function userWithTeam(): User
    {
        $user = User::factory()->create();
        $team = $user->ownedTeams()->create([
            'name' => 'Test Team',
            'personal_team' => true,
        ]);
        $user->forceFill(['current_team_id' => $team->id])->save();

        return $user;
    }
}
