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
            'year' => ['required', 'string', 'max:255'],
            'from' => ['required', 'string', 'max:255'],
            'message' => ['required'],
        ])->validateWithBag('updateInvoiceInformation');

        $invoice->fill([
            'email' => $input['email'],
            'subs' => $input['subs'],
            'date' => $input['date'],
            'year' => $input['year'],
            'from' => $input['from'],
            'message' => $input['message'],
        ])->save();
    }
}
