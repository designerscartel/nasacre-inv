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

        $sacres = Sacre::all();

        if (!empty($sacres)) {

            $iteration = 1;

            foreach ($sacres as $scare) {

                $delaySeconds = $iteration += 5;

                $contact = $this->checkContact($scare->contacts);
                $details = [
                    'sacre' => $scare,
                    'invoice' => $invoice,
                    'contact' => $contact
                ];
                SendInvoice::dispatch($details)->delay(now()->addSeconds($delaySeconds));
            }

            $invoice->batch = ($invoice->batch + 1);
            $invoice->save();

        }

    }


    /**
     * @param $contacts
     * @return mixed
     */
    private function checkContact($contacts)
    {
        $myContact = $contacts->where('default', 1)->first();

        if (empty($myContact)) {
            if (!empty($contacts)) {
                foreach ($contacts as $sContact) {
                    if ($sContact->position_id === 2) {
                        if (!empty($sContact->email)) {
                            $myContact = $sContact;
                            break;
                        }
                    } elseif ($sContact->position_id === 3) {
                        if (!empty($sContact->email)) {
                            $myContact = $sContact;
                            break;
                        }
                    } elseif ($sContact->position_id === 4) {
                        if (!empty($sContact->email)) {
                            $myContact = $sContact;
                            break;
                        }
                    } elseif ($sContact->position_id === 1) {
                        if (!empty($sContact->email)) {
                            $myContact = $sContact;
                            break;
                        }
                    }
                }
            }
        }

        return $myContact;
    }


}
