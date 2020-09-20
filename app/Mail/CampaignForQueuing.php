<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\CampaignEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignForQueuing extends Mailable
{
    use Queueable, SerializesModels;

    protected $campaign;

    protected $email;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Campaign $campaign, CampaignEmail $email)
    {
        //
        $this->campaign = $campaign;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('mail@example.com', 'Mailtrap')
            ->subject($this->campaign->subject)
            ->with([
                'campaign' => $this->campaign,
                'email' => $this->email
            ])
            ->markdown('mails.campaign-email');
    }
}
