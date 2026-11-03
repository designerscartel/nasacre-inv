<?php

namespace App\Jobs;

use App\Mail\CampaignForQueuing;
use App\Models\CampaignDelivery;
use App\Models\CampaignEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $details;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recipient = $this->details['email'] ?? new CampaignEmail([
            'name' => $this->details['recipient_name'],
            'email' => $this->details['recipient_email'],
        ]);
        $email = new CampaignForQueuing($this->details['campaign'], $recipient);
        Mail::to($recipient->email)->send($email);

        if (! empty($this->details['campaign_delivery_id'])) {
            CampaignDelivery::whereKey($this->details['campaign_delivery_id'])->update([
                'delivery_status' => 'sent',
                'sent_at' => now(),
                'failed_at' => null,
                'failure_message' => null,
            ]);
        }
    }

    public function failed(Throwable $exception)
    {
        if (! empty($this->details['campaign_delivery_id'])) {
            CampaignDelivery::whereKey($this->details['campaign_delivery_id'])->update([
                'delivery_status' => 'failed',
                'failed_at' => now(),
                'failure_message' => substr($exception->getMessage(), 0, 2000),
            ]);
        }
    }
}
