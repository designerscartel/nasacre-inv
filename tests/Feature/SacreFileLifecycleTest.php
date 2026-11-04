<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\ContactPosition;
use App\Models\Invoice;
use App\Models\Region;
use App\Models\Sacre;
use App\Models\SacreBooking;
use App\Models\SacreContact;
use App\Models\SacreFile;
use App\Models\SacreInvoice;
use App\Models\SharedFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SacreFileLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_file_renames_move_the_stored_pdf_and_suffix_collisions()
    {
        Storage::fake();
        $this->actingAs(User::factory()->create());
        $sacre = $this->sacre();
        $file = SacreFile::create([
            'sacre_id' => $sacre->id,
            'filename' => 'original.pdf',
            'year' => '2026',
        ]);
        Storage::put('files/original.pdf', 'original');

        $this->put(route('sacre-files.update', [$sacre, $file]), [
            'filename' => 'renamed.pdf',
            'year' => '2027',
        ])->assertStatus(303);

        Storage::assertMissing('files/original.pdf');
        Storage::assertExists('files/renamed.pdf');
        $this->assertSame('renamed.pdf', $file->refresh()->filename);

        Storage::put('files/existing.pdf', 'existing');
        Storage::put('files/existing-2.pdf', 'existing 2');
        $this->put(route('sacre-files.update', [$sacre, $file]), [
            'filename' => 'existing.pdf',
            'year' => '2028',
        ])->assertStatus(303);

        Storage::assertMissing('files/renamed.pdf');
        Storage::assertExists('files/existing.pdf');
        Storage::assertExists('files/existing-2.pdf');
        Storage::assertExists('files/existing-3.pdf');
        $this->assertSame('existing-3.pdf', $file->refresh()->filename);
        $this->assertSame('2028', $file->year);
    }

    public function test_shared_file_renames_suffix_collisions_in_the_common_directory()
    {
        Storage::fake();
        $this->actingAs(User::factory()->create());
        $sacre = $this->sacre();
        $file = SharedFile::create([
            'sacre_id' => $sacre->id,
            'filename' => 'shared.pdf',
        ]);
        Storage::put('files/shared.pdf', 'shared');
        Storage::put('files/report.pdf', 'report');

        $this->put(route('share-files.update', [$sacre, $file]), [
            'filename' => 'report.pdf',
        ])->assertStatus(303);

        Storage::assertMissing('files/shared.pdf');
        Storage::assertExists('files/report.pdf');
        Storage::assertExists('files/report-2.pdf');
        $this->assertSame('report-2.pdf', $file->refresh()->filename);
    }

    public function test_explicit_file_deletion_removes_stored_pdfs_and_archives_records()
    {
        Storage::fake();
        $this->actingAs(User::factory()->create());
        $sacre = $this->sacre();
        $file = SacreFile::create([
            'sacre_id' => $sacre->id,
            'filename' => 'report.pdf',
            'year' => '2026',
        ]);
        $sharedFile = SharedFile::create([
            'sacre_id' => $sacre->id,
            'filename' => 'shared.pdf',
        ]);
        Storage::put('files/report.pdf', 'report');
        Storage::put('files/shared.pdf', 'shared');

        $this->delete(route('sacre-files.destroy', [$sacre, $file]))->assertStatus(303);
        $this->delete(route('share-files.destroy', [$sacre, $sharedFile]))->assertStatus(303);

        Storage::assertMissing('files/report.pdf');
        Storage::assertMissing('files/shared.pdf');
        $this->assertSoftDeleted('sacre_files', ['id' => $file->id]);
        $this->assertSoftDeleted('shared_files', ['id' => $sharedFile->id]);
    }

    public function test_deleting_a_sacre_archives_child_records_but_preserves_files_and_history()
    {
        Storage::fake();
        $this->actingAs(User::factory()->create());
        $sacre = $this->sacre();
        $position = ContactPosition::create(['title' => 'Officer']);
        $contact = SacreContact::create([
            'sacre_id' => $sacre->id,
            'position_id' => $position->id,
            'name' => 'Contact',
            'email' => 'contact@example.test',
        ]);
        $file = SacreFile::create([
            'sacre_id' => $sacre->id,
            'filename' => 'report.pdf',
            'year' => '2026',
        ]);
        $sharedFile = SharedFile::create([
            'sacre_id' => $sacre->id,
            'filename' => 'shared.pdf',
        ]);
        Storage::put('files/report.pdf', 'report');
        Storage::put('files/shared.pdf', 'shared');

        $invoice = Invoice::create([
            'email' => 'sender@example.test',
            'subs' => 100,
            'year' => 2026,
            'from' => 'NASACRE',
            'message' => 'Invoice',
        ]);
        $sacreInvoice = SacreInvoice::create([
            'invoice_id' => $invoice->id,
            'sacre_id' => $sacre->id,
        ]);
        $booking = Booking::create([
            'email' => 'sender@example.test',
            'subscribed' => 100,
            'none_subscribed' => 150,
            'year' => 2026,
            'from' => 'NASACRE',
            'message' => 'Booking',
        ]);
        $sacreBooking = SacreBooking::create([
            'booking_id' => $booking->id,
            'sacre_id' => $sacre->id,
        ]);

        $this->delete(route('sacres.destroy', $sacre))->assertStatus(303);

        $this->assertSoftDeleted('sacres', ['id' => $sacre->id]);
        $this->assertSoftDeleted('sacre_contacts', ['id' => $contact->id]);
        $this->assertSoftDeleted('sacre_files', ['id' => $file->id]);
        $this->assertSoftDeleted('shared_files', ['id' => $sharedFile->id]);
        Storage::assertExists('files/report.pdf');
        Storage::assertExists('files/shared.pdf');
        $this->assertDatabaseHas('sacre_invoices', ['id' => $sacreInvoice->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('sacre_bookings', ['id' => $sacreBooking->id, 'deleted_at' => null]);
    }

    private function sacre()
    {
        $region = Region::create(['title' => 'Region']);

        return Sacre::create([
            'region_id' => $region->id,
            'title' => 'Test SACRE',
            'code' => 100,
        ]);
    }
}
