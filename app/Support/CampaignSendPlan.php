<?php

namespace App\Support;

class CampaignSendPlan
{
    public function forCampaign($campaign)
    {
        $campaign->loadMissing('group.emails', 'deliveries');
        $recipients = collect();
        $skipped = collect();

        $campaign->group->emails->each(function ($email) use ($recipients, $skipped) {
            if (filter_var($email->email, FILTER_VALIDATE_EMAIL)) {
                $recipients->push($email);
                return;
            }

            $skipped->push([
                'id' => $email->id,
                'name' => $email->name,
                'email' => $email->email,
                'reason' => empty($email->email)
                    ? 'No email address is recorded.'
                    : 'The email address is invalid.',
            ]);
        });

        return [
            'batch' => ((int) $campaign->batch) + 1,
            'has_been_sent' => ((int) $campaign->batch) > 0 || $campaign->deliveries->isNotEmpty(),
            'group' => $campaign->group,
            'recipient_count' => $recipients->count(),
            'skipped_count' => $skipped->count(),
            'recipients' => $recipients->values(),
            'skipped' => $skipped->values(),
        ];
    }

    public function preview($campaign)
    {
        $plan = $this->forCampaign($campaign);

        return [
            'batch' => $plan['batch'],
            'has_been_sent' => $plan['has_been_sent'],
            'group' => [
                'id' => $plan['group']->id,
                'title' => $plan['group']->title,
            ],
            'recipient_count' => $plan['recipient_count'],
            'skipped_count' => $plan['skipped_count'],
            'recipients' => $plan['recipients']->map(function ($recipient) {
                return [
                    'id' => $recipient->id,
                    'name' => $recipient->name,
                    'email' => $recipient->email,
                ];
            })->values(),
            'skipped' => $plan['skipped'],
        ];
    }
}
