<?php

namespace Tests\Feature;

use App\Models\PlaceArea;
use App\Models\PlaceOfInterest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlacesOfInterestTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_import_creates_places_areas_and_multi_area_links(): void
    {
        $this->artisan('places:import')
            ->expectsOutput('Imported 41 places of interest.')
            ->assertSuccessful();

        $this->assertDatabaseCount('places_of_interest', 41);
        $this->assertDatabaseCount('place_areas', 10);
        $this->assertDatabaseHas('place_areas', ['name' => 'London']);
        $this->assertTrue(PlaceOfInterest::query()->where('do_not_list', true)->exists());
        $this->assertTrue(PlaceOfInterest::query()->whereHas('areas', fn ($query) => $query->where('name', 'London'))->exists());
        $this->assertTrue(PlaceOfInterest::query()->has('areas', '>', 1)->exists());

        $this->artisan('places:import')->assertSuccessful();
        $this->assertDatabaseCount('places_of_interest', 41);
        $this->assertDatabaseCount('place_areas', 10);
    }

    public function test_authenticated_user_can_manage_places(): void
    {
        $this->actingAs($this->userWithTeam());
        $london = PlaceArea::create(['name' => 'London']);
        $southEast = PlaceArea::create(['name' => 'South East']);

        $this->post(route('places.store'), [
            'name' => 'Test Centre',
            'religion' => 'Humanism',
            'contact_name' => 'Alex Example',
            'organisation_email' => 'hello@example.com',
            'personal_email' => null,
            'phone' => '01234 567890',
            'website' => 'https://example.com',
            'last_training' => 'March 2026',
            'charge_to_speak' => 'No charge',
            'notes' => 'Test notes',
            'do_not_list' => false,
            'area_ids' => [$london->id, $southEast->id],
        ])->assertRedirect();

        $place = PlaceOfInterest::firstOrFail();
        $this->assertCount(2, $place->areas);

        $this->put(route('places.update', $place), [
            'name' => 'Updated Centre',
            'religion' => null,
            'contact_name' => null,
            'organisation_email' => null,
            'personal_email' => null,
            'phone' => null,
            'website' => null,
            'last_training' => null,
            'charge_to_speak' => null,
            'notes' => null,
            'do_not_list' => true,
            'area_ids' => [$london->id],
        ])->assertRedirect();

        $this->assertDatabaseHas('places_of_interest', [
            'id' => $place->id,
            'name' => 'Updated Centre',
            'do_not_list' => true,
        ]);
        $this->assertCount(1, $place->fresh()->areas);

        $this->delete(route('places.destroy', $place))->assertRedirect();
        $this->assertSoftDeleted($place);
        $this->assertDatabaseMissing('place_area_place_of_interest', ['place_of_interest_id' => $place->id]);
    }

    public function test_place_directory_can_be_filtered_by_area(): void
    {
        $user = $this->userWithTeam();
        $this->actingAs($user);
        $london = PlaceArea::create(['name' => 'London']);
        $wales = PlaceArea::create(['name' => 'Wales']);
        $londonPlace = PlaceOfInterest::create(['name' => 'London Centre']);
        $walesPlace = PlaceOfInterest::create(['name' => 'Wales Centre']);
        $londonPlace->areas()->attach($london);
        $walesPlace->areas()->attach($wales);

        $response = $this->get(route('places.index', ['area' => $london->id]), [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $response->assertJsonPath('props.filters.area', (string) $london->id);
        $response->assertJsonCount(1, 'props.places.data');
        $response->assertJsonPath('props.places.data.0.name', 'London Centre');
    }

    public function test_area_with_places_cannot_be_deleted(): void
    {
        $this->actingAs($this->userWithTeam());
        $area = PlaceArea::create(['name' => 'London']);
        $place = PlaceOfInterest::create(['name' => 'Test Centre']);
        $place->areas()->attach($area);

        $this->delete(route('place-areas.destroy', $area))
            ->assertSessionHasErrors('area');

        $this->assertDatabaseHas('place_areas', ['id' => $area->id, 'deleted_at' => null]);

        $place->areas()->detach();
        $this->delete(route('place-areas.destroy', $area))->assertRedirect();
        $this->assertSoftDeleted($area);
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
