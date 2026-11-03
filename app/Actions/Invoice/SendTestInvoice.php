<?php

namespace App\Actions\Invoice;

use App\Contracts\Invoice\CreatesInvoicePdf;
use App\Mail\InvoiceForQueuing;
use App\Models\Sacre;
use App\Models\SacreContact;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SendTestInvoice
{
    public function send($invoice, array $input)
    {
        Validator::make($input, [
            'email' => ['required', 'email', 'max:255'],
            'sacre_id' => [
                'required',
                'integer',
                Rule::exists('sacres', 'id')->whereNull('deleted_at'),
            ],
        ])->validateWithBag('sendTestInvoice');

        $sacre = Sacre::findOrFail($input['sacre_id']);
        $contact = new SacreContact([
            'name' => 'Test Recipient',
            'email' => $input['email'],
        ]);
        $pdf = app(CreatesInvoicePdf::class)->output($invoice, $sacre);

        Mail::to($contact->email)->send(
            new InvoiceForQueuing($invoice, $sacre, $contact, $pdf, true)
        );
    }
}
