<?php

namespace Tests\Feature;

use App\Models\Region;
use App\Models\Sacre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SacreDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_sacre_index_redirects_to_the_directory_dashboard()
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('sacres.index'))
            ->assertRedirect('/dashboard');
    }

    public function test_dashboard_uses_a_lightweight_searchable_sacre_directory()
    {
        $user = User::factory()->create();
        $team = $user->ownedTeams()->create([
            'name' => 'Test Team',
            'personal_team' => true,
        ]);
        $user->forceFill(['current_team_id' => $team->id])->save();
        $this->actingAs($user);
        $north = Region::create(['title' => 'North']);
        $south = Region::create(['title' => 'South']);
        Sacre::create([
            'region_id' => $north->id,
            'title' => 'Alpha SACRE',
            'member' => true,
            'code' => 101,
        ]);
        Sacre::create([
            'region_id' => $south->id,
            'title' => 'Beta SACRE',
            'member' => false,
            'code' => 202,
        ]);

        $response = $this->get(route('dashboard', ['search' => 'North']), [
            'X-Inertia' => 'true',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $response->assertJsonPath('props.filters.search', 'North');
        $response->assertJsonPath('props.auth.user.id', $user->id);
        $response->assertJsonPath('props.auth.user.current_team.id', $team->id);
        $this->assertNotEmpty($response->json('props.auth.user.profile_photo_url'));
        $response->assertJsonPath('props.summary.total', 2);
        $response->assertJsonPath('props.summary.members', 1);
        $response->assertJsonPath('props.sacres.meta.total', 1);
        $response->assertJsonPath('props.sacres.data.0.title', 'Alpha SACRE');
        $this->assertArrayNotHasKey('contacts', $response->json('props.sacres.data.0'));
        $this->assertArrayNotHasKey('invoices', $response->json('props.sacres.data.0'));
        $this->assertArrayNotHasKey('files', $response->json('props.sacres.data.0'));
    }
}
