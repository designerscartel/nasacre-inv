<?php

namespace Tests\Feature;

use App\Jobs\SendCampaign;
use App\Mail\CampaignForQueuing;
use App\Models\Campaign;
use App\Models\CampaignDelivery;
use App\Models\CampaignEmail;
use App\Models\CampaignGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use Tests\TestCase;

class CampaignDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_sending_a_campaign_creates_a_tracked_batch_for_valid_recipients()
    {
        Queue::fake();
        $this->actingAs(User::factory()->create());
        list($campaign, $group) = $this->campaign();
        $this->recipient($group, 'First Recipient', 'first@example.test');
        $this->recipient($group, 'Second Recipient', 'second@example.test');
        $this->recipient($group, 'Invalid Recipient', 'invalid-email');

        $response = $this->post(route('campaigns.send', $campaign), ['send' => true]);

        $response->assertStatus(303);
        $this->assertSame(1, $campaign->refresh()->batch);
        $this->assertSame(2, CampaignDelivery::count());
        $this->assertDatabaseHas('campaign_deliveries', [
            'campaign_id' => $campaign->id,
            'batch' => 1,
            'delivery_status' => 'queued',
            'recipient_name' => 'First Recipient',
            'recipient_email' => 'first@example.test',
        ]);
        Queue::assertPushed(SendCampaign::class, 2);
    }

    public function test_resending_requires_confirmation_and_creates_a_new_batch()
    {
        Queue::fake();
        $this->actingAs(User::factory()->create());
        list($campaign, $group) = $this->campaign(['batch' => 1]);
        $this->recipient($group, 'Recipient', 'recipient@example.test');
        CampaignDelivery::create([
            'campaign_id' => $campaign->id,
            'batch' => 1,
            'delivery_status' => 'sent',
            'recipient_name' => 'Recipient',
            'recipient_email' => 'recipient@example.test',
        ]);

        $response = $this->from(route('campaigns.index'))->post(route('campaigns.send', $campaign), [
            'send' => true,
        ]);

        $response->assertRedirect(route('campaigns.index'));
        $response->assertSessionHasErrors(['confirm_resend'], null, 'sendCampaignInformation');
        $this->assertSame(1, $campaign->refresh()->batch);
        $this->assertSame(1, CampaignDelivery::count());
        Queue::assertNothingPushed();

        $this->post(route('campaigns.send', $campaign), [
            'send' => true,
            'confirm_resend' => true,
        ])->assertStatus(303);

        $this->assertSame(2, $campaign->refresh()->batch);
        $this->assertDatabaseHas('campaign_deliveries', [
            'campaign_id' => $campaign->id,
            'batch' => 2,
            'delivery_status' => 'queued',
        ]);
        Queue::assertPushed(SendCampaign::class, 1);
    }

    public function test_campaign_jobs_record_success_and_failure()
    {
        Mail::fake();
        list($campaign) = $this->campaign();
        $sent = $this->delivery($campaign, 'sent@example.test');
        $sentJob = $this->job($campaign, $sent);

        $sentJob->handle();

        Mail::assertSent(CampaignForQueuing::class, function ($mail) {
            return $mail->hasTo('sent@example.test');
        });
        $this->assertSame('sent', $sent->refresh()->delivery_status);
        $this->assertNotNull($sent->sent_at);

        $failed = $this->delivery($campaign, 'failed@example.test');
        $failedJob = $this->job($campaign, $failed);
        $failedJob->failed(new RuntimeException('Mailbox unavailable'));

        $this->assertSame('failed', $failed->refresh()->delivery_status);
        $this->assertNotNull($failed->failed_at);
        $this->assertSame('Mailbox unavailable', $failed->failure_message);
    }

    public function test_it_summarises_the_latest_campaign_batch()
    {
        list($campaign) = $this->campaign(['batch' => 2]);
        CampaignDelivery::create([
            'campaign_id' => $campaign->id,
            'batch' => 1,
            'delivery_status' => 'failed',
            'recipient_email' => 'old@example.test',
        ]);
        $this->delivery($campaign, 'sent@example.test', 'sent', 2);
        $this->delivery($campaign, 'queued@example.test', 'queued', 2);
        $this->delivery($campaign, 'failed@example.test', 'failed', 2);
        $campaign->load('deliveries');

        $summary = $campaign->latestDeliverySummary();

        $this->assertSame(2, $summary['batch']);
        $this->assertSame(3, $summary['total']);
        $this->assertSame(1, $summary['sent']);
        $this->assertSame(1, $summary['queued']);
        $this->assertSame(1, $summary['failed']);
    }

    public function test_retrying_failed_deliveries_requires_confirmation()
    {
        Queue::fake();
        $this->actingAs(User::factory()->create());
        list($campaign) = $this->campaign(['batch' => 1]);
        $this->delivery($campaign, 'failed@example.test', 'failed');

        $response = $this->from(route('campaigns.index'))
            ->post(route('campaigns.retry-failed', $campaign), ['retry_batch' => 1]);

        $response->assertRedirect(route('campaigns.index'));
        $response->assertSessionHasErrors(['confirm_retry'], null, 'retryFailedCampaignDeliveries');
        $this->assertSame(1, $campaign->refresh()->batch);
        $this->assertSame(1, CampaignDelivery::count());
        Queue::assertNothingPushed();
    }

    public function test_retrying_creates_a_new_batch_for_only_latest_failed_deliveries()
    {
        Queue::fake();
        $this->actingAs(User::factory()->create());
        list($campaign) = $this->campaign(['batch' => 2]);
        $this->delivery($campaign, 'old-failure@example.test', 'failed', 1);
        $this->delivery($campaign, 'sent@example.test', 'sent', 2);
        $this->delivery($campaign, 'queued@example.test', 'queued', 2);
        $this->delivery($campaign, 'failed@example.test', 'failed', 2);

        $this->post(route('campaigns.retry-failed', $campaign), [
            'confirm_retry' => true,
            'retry_batch' => 2,
        ])->assertStatus(303);

        $this->assertSame(3, $campaign->refresh()->batch);
        $this->assertDatabaseHas('campaign_deliveries', [
            'campaign_id' => $campaign->id,
            'batch' => 3,
            'delivery_status' => 'queued',
            'recipient_email' => 'failed@example.test',
        ]);
        $this->assertDatabaseMissing('campaign_deliveries', [
            'campaign_id' => $campaign->id,
            'batch' => 3,
            'recipient_email' => 'sent@example.test',
        ]);
        $this->assertSame(1, CampaignDelivery::where('batch', 3)->count());
        Queue::assertPushed(SendCampaign::class, 1);
    }

    public function test_retry_does_nothing_when_latest_batch_has_no_failures()
    {
        Queue::fake();
        $this->actingAs(User::factory()->create());
        list($campaign) = $this->campaign(['batch' => 2]);
        $this->delivery($campaign, 'old-failure@example.test', 'failed', 1);
        $this->delivery($campaign, 'sent@example.test', 'sent', 2);

        $this->post(route('campaigns.retry-failed', $campaign), [
            'confirm_retry' => true,
            'retry_batch' => 2,
        ])->assertStatus(303);

        $this->assertSame(2, $campaign->refresh()->batch);
        $this->assertSame(2, CampaignDelivery::count());
        Queue::assertNothingPushed();
    }

    public function test_it_summarises_all_campaign_batches_with_timestamps()
    {
        list($campaign) = $this->campaign(['batch' => 2]);
        $old = $this->delivery($campaign, 'old@example.test', 'sent', 1);
        $old->update(['sent_at' => now()]);
        $failed = $this->delivery($campaign, 'failed@example.test', 'failed', 2);
        $failed->update([
            'failed_at' => now(),
            'failure_message' => 'Mailbox unavailable',
        ]);
        $campaign->load('deliveries');

        $history = $campaign->deliveryBatchHistory();

        $this->assertSame([2, 1], $history->pluck('batch')->all());
        $this->assertSame(1, $history->first()['failed']);
        $this->assertSame(1, $history->first()['retryable_failed']);
        $this->assertSame('Mailbox unavailable', $history->first()['recipients']->first()['failure_message']);
        $this->assertNotNull($history->first()['recipients']->first()['failed_at']);
        $this->assertNotNull($history->last()['recipients']->first()['sent_at']);
    }

    public function test_retrying_an_older_batch_skips_recipients_with_newer_attempts()
    {
        Queue::fake();
        $this->actingAs(User::factory()->create());
        list($campaign) = $this->campaign(['batch' => 2]);
        $this->delivery($campaign, 'already-retried@example.test', 'failed', 1);
        $this->delivery($campaign, 'retry-me@example.test', 'failed', 1);
        $this->delivery($campaign, 'already-retried@example.test', 'failed', 2);

        $campaign->load('deliveries');
        $this->assertSame(1, $campaign->deliveryBatchHistory()->last()['retryable_failed']);

        $this->post(route('campaigns.retry-failed', $campaign), [
            'confirm_retry' => true,
            'retry_batch' => 1,
        ])->assertStatus(303);

        $this->assertSame(3, $campaign->refresh()->batch);
        $this->assertDatabaseHas('campaign_deliveries', [
            'batch' => 3,
            'recipient_email' => 'retry-me@example.test',
        ]);
        $this->assertDatabaseMissing('campaign_deliveries', [
            'batch' => 3,
            'recipient_email' => 'already-retried@example.test',
        ]);
        Queue::assertPushed(SendCampaign::class, 1);
    }

    private function campaign(array $attributes = [])
    {
        $group = CampaignGroup::create(['title' => 'Campaign Group']);
        $campaign = Campaign::create(array_merge([
            'campaign_group_id' => $group->id,
            'email' => 'admin@nasacre.org.uk',
            'subject' => 'Campaign subject',
            'url' => 'https://example.test/campaign',
            'message' => 'Campaign message.',
            'batch' => 0,
        ], $attributes));

        return [$campaign, $group];
    }

    private function recipient($group, $name, $email)
    {
        return CampaignEmail::create([
            'campaign_group_id' => $group->id,
            'name' => $name,
            'email' => $email,
        ]);
    }

    private function delivery($campaign, $email, $status = 'queued', $batch = 1)
    {
        return CampaignDelivery::create([
            'campaign_id' => $campaign->id,
            'batch' => $batch,
            'delivery_status' => $status,
            'recipient_name' => 'Recipient',
            'recipient_email' => $email,
            'queued_at' => now(),
        ]);
    }

    private function job($campaign, $delivery)
    {
        return new SendCampaign([
            'campaign' => $campaign,
            'recipient_name' => $delivery->recipient_name,
            'recipient_email' => $delivery->recipient_email,
            'campaign_delivery_id' => $delivery->id,
        ]);
    }
}
