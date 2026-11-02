<?php

namespace App\Support;

use Illuminate\Support\Collection;

class InvoiceContactSelector
{
    public function select(Collection $contacts)
    {
        return $this->eligible($contacts)->first();
    }

    public function eligible(Collection $contacts)
    {
        return $contacts
            ->filter(function ($contact) {
                return filter_var($contact->email, FILTER_VALIDATE_EMAIL);
            })
            ->sortBy(function ($contact) {
                if ((bool) $contact->default) {
                    return 0;
                }

                $priority = [2 => 1, 3 => 2, 4 => 3, 1 => 4];
                $positionId = $contact->position_id;

                return $positionId === null ? 5 : ($priority[$positionId] ?? 5);
            })
            ->values();
    }
}
