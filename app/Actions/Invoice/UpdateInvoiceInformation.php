<?php

namespace App\Actions\Invoice;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Invoice\UpdatesInvoiceInformation;

class UpdateInvoiceInformation implements UpdatesInvoiceInformation
{
    /**
     * Validate and update the given campaign's information.
     *
     * @param  mixed  $invoice
     * @param  array  $input
     * @return void
     */
    public function update($invoice, array $input)
    {
        Validator::make($input, [
            'email' => ['required', 'string', 'max:255'],
            'subs' => ['required', 'string', 'max:255'],
            'date' => ['required', 'string', 'max:255'],
            'from' => ['required', 'string', 'max:255'],
            'message' => ['required'],
        ])->validateWithBag('updateInvoiceInformation');

        $date = \Carbon\Carbon::parse($input['date']);

        $invoice->fill([
            'email' => $input['email'],
            'subs' => $input['subs'],
            'date' => $date->toDateTimeString(),
            'year' => $date->format('Y'),
            'from' => $input['from'],
            'message' => $input['message'],
        ])->save();
    }
}
