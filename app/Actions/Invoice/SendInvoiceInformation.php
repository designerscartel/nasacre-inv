<?php

namespace App\Actions\Invoice;

use App\Contracts\Invoice\SendsInvoiceInformation;
use App\Jobs\SendInvoice;
use App\Models\Sacre;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class SendInvoiceInformation
 * @package App\Actions\Invoice
 */
class SendInvoiceInformation implements SendsInvoiceInformation
{
    /**
     * Validate and Sends the given invoice's information.
     *
     * @param mixed $invoice
     * @param array $input
     * @return void
     */
    public function send($invoice, array $input)
    {

        Validator::make($input, [
            'send' => ['required'],
        ])->validateWithBag('sendInvoiceInformation');

        // $sacres = Sacre::all();
        $sacres = Sacre::limit(1)->get();

        if (!empty($sacres)) {
            foreach ($sacres as $scare) {

                $contact = $scare->contacts->where('default', 1)->first();

                $details = [
                    'sacre' => $scare,
                    'invoice' => $invoice,
                    'contact' => $contact
                ];

                SendInvoice::dispatch($details);
            }
        }

    }
}
