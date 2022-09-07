<?php

namespace App\Actions\SacreInvoice;

use App\Contracts\SacreInvoice\UpdatesSacreInvoiceInformation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateSacreInvoiceInformation implements UpdatesSacreInvoiceInformation
{
    /**
     * Validate and update the given campaign's information.
     *
     * @param  mixed  $sacreInvoice
     * @param  array  $input
     * @return void
     */
    public function update($sacreInvoice, array $input)
    {
        Validator::make($input, [
            'po_number' => [],
            'address' => [],
            'date' => [],
        ])->validateWithBag('updateSacreInvoiceInformation');

        $date = null;
        if($input['date']) {
            $date = \Carbon\Carbon::parse($input['date'])->toDateTimeString();
        }
        $sacreInvoice->fill([
            'po_number' => $input['po_number'],
            'address' => $input['address'],
            'date' => $date,
            'virtual_training' => $input['virtual_training'],
        ])->save();
    }
}
