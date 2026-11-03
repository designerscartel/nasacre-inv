<?php

namespace Tests\Feature;

use App\Jobs\SendCampaign;
use App\Mail\CampaignForQueuing;
use App\Models\Campaign;
use App\Models\CampaignEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CampaignTestEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_send_a_personalised_test_campaign_without_queueing_the_group()
    {
        Mail::fake();
        Queue::fake();
        $this->actingAs(User::factory()->create());
        $campaign = $this->campaign();

        $response = $this->post(route('campaigns.send-test', $campaign), [
            'name' => 'Campaign Reviewer',
            'email' => 'reviewer@example.test',
        ]);

        $response->assertStatus(303);
        Mail::assertSent(CampaignForQueuing::class, function ($mail) {
            $mail->build();

            return $mail->hasTo('reviewer@example.test')
                && $mail->subject === '[TEST] Important campaign update'
                && $mail->viewData['email']->name === 'Campaign Reviewer';
        });
        Queue::assertNotPushed(SendCampaign::class);
        $this->assertSame(0, CampaignEmail::count());
        $this->assertSame('Important campaign update', $campaign->refresh()->subject);
    }

    public function test_test_campaign_validation_rejects_invalid_input_without_sending_mail()
    {
        Mail::fake();
        Queue::fake();
        $this->actingAs(User::factory()->create());
        $campaign = $this->campaign();

        $response = $this->from(route('campaigns.show', $campaign))->post(route('campaigns.send-test', $campaign), [
            'name' => '',
            'email' => 'not-an-email',
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));
        $response->assertSessionHasErrors(['name', 'email'], null, 'sendTestCampaign');
        Mail::assertNothingSent();
        Queue::assertNotPushed(SendCampaign::class);
        $this->assertSame(0, CampaignEmail::count());
    }

    public function test_a_guest_cannot_send_a_test_campaign()
    {
        Mail::fake();
        $campaign = $this->campaign();

        $response = $this->post(route('campaigns.send-test', $campaign), [
            'name' => 'Campaign Reviewer',
            'email' => 'reviewer@example.test',
        ]);

        $response->assertRedirect('/login');
        Mail::assertNothingSent();
    }

    private function campaign()
    {
        return Campaign::create([
            'campaign_group_id' => 1,
            'email' => 'admin@nasacre.org.uk',
            'subject' => 'Important campaign update',
            'url' => 'https://example.test/campaign',
            'message' => 'This is the campaign message.',
        ]);
    }
}
