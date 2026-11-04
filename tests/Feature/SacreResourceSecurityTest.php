<?php

namespace Tests\Feature;

use App\Models\ContactPosition;
use App\Models\Region;
use App\Models\Sacre;
use App\Models\SacreContact;
use App\Models\SacreFile;
use App\Models\SharedFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SacreResourceSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_contacts_cannot_be_updated_or_deleted_through_another_sacre()
    {
        $this->actingAs(User::factory()->create());
        list($firstSacre, $secondSacre) = $this->sacres();
        $position = ContactPosition::create(['title' => 'Officer']);
        $contact = SacreContact::create([
            'sacre_id' => $firstSacre->id,
            'position_id' => $position->id,
            'name' => 'Original Contact',
            'email' => 'original@example.test',
            'default' => false,
        ]);

        $this->put(route('sacre-contacts.update', [$secondSacre, $contact]), [
            'position_id' => $position->id,
            'name' => 'Changed Contact',
            'email' => 'changed@example.test',
            'tel' => '',
            'role_dscpn' => '',
            'default' => false,
        ])->assertNotFound();

        $this->delete(route('sacre-contacts.destroy', [$secondSacre, $contact]))
            ->assertNotFound();

        $this->assertSame('Original Contact', $contact->refresh()->name);
        $this->assertNull($contact->deleted_at);
    }

    public function test_sacre_files_cannot_be_read_updated_or_deleted_through_another_sacre()
    {
        $this->actingAs(User::factory()->create());
        list($firstSacre, $secondSacre) = $this->sacres();
        $file = SacreFile::create([
            'sacre_id' => $firstSacre->id,
            'filename' => 'original.pdf',
            'year' => '2026',
        ]);

        $this->get(route('sacre-files.show', [$secondSacre, $file]))->assertNotFound();
        $this->get(route('api.file', [$secondSacre, $file]))->assertNotFound();
        $this->put(route('sacre-files.update', [$secondSacre, $file]), [
            'filename' => 'changed.pdf',
            'year' => '2027',
        ])->assertNotFound();
        $this->delete(route('sacre-files.destroy', [$secondSacre, $file]))->assertNotFound();

        $this->assertSame('original.pdf', $file->refresh()->filename);
        $this->assertNull($file->deleted_at);
    }

    public function test_shared_files_cannot_be_read_updated_or_deleted_through_another_sacre()
    {
        $this->actingAs(User::factory()->create());
        list($firstSacre, $secondSacre) = $this->sacres();
        $file = SharedFile::create([
            'sacre_id' => $firstSacre->id,
            'filename' => 'original.pdf',
        ]);

        $this->get(route('share-files.show', [$secondSacre, $file]))->assertNotFound();
        $this->get(route('api.shared-file', [$secondSacre, $file]))->assertNotFound();
        $this->put(route('share-files.update', [$secondSacre, $file]), [
            'filename' => 'changed.pdf',
        ])->assertNotFound();
        $this->delete(route('share-files.destroy', [$secondSacre, $file]))->assertNotFound();

        $this->assertSame('original.pdf', $file->refresh()->filename);
        $this->assertNull($file->deleted_at);
    }

    private function sacres()
    {
        $region = Region::create(['title' => 'Region']);

        return [
            Sacre::create(['region_id' => $region->id, 'title' => 'First SACRE', 'code' => 100]),
            Sacre::create(['region_id' => $region->id, 'title' => 'Second SACRE', 'code' => 200]),
        ];
    }
}
