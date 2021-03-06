<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Invoice\CreatesInvoiceInformation;

class CreateInvoiceInformation implements CreatesInvoiceInformation
{
    /**
     * Validate and Create the given campaign's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'email' => ['required', 'string', 'max:255'],
            'subs' => ['required', 'string', 'max:255'],
            'date' => ['required', 'string', 'max:255'],
            'from' => ['required', 'string', 'max:255'],
            'message' => ['required'],
        ])->validateWithBag('createInvoiceInformation');

        $invoice = new Invoice();
        $date = \Carbon\Carbon::parse($input['date']);

        return $invoice->create([
            'email' => $input['email'],
            'subs' => $input['subs'],
            'date' => $date->toDateTimeString(),
            'year' => $date->format('Y'),
            'from' => $input['from'],
            'message' => $input['message'],
        ]);

    }
}
