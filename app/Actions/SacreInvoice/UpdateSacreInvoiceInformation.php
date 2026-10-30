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
            'po_number' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'virtual_training' => ['nullable', 'boolean'],
            'total_override' => ['nullable', 'numeric', 'min:0'],
        ])->validateWithBag('updateSacreInvoiceInformation');

        $date = null;
        if(!empty($input['date'])) {
            $date = \Carbon\Carbon::parse($input['date'])->toDateTimeString();
        }
        $sacreInvoice->fill([
            'po_number' => $input['po_number'] ?? null,
            'address' => $input['address'] ?? null,
            'date' => $date,
            'virtual_training' => $input['virtual_training'] ?? false,
            'total_override' => $input['total_override'] ?? null,
        ])->save();
    }
}
