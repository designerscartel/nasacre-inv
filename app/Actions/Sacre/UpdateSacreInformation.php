<?php

namespace App\Actions\Sacre;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Sacre\UpdatesSacreInformation;

class UpdateSacreInformation implements UpdatesSacreInformation
{
    /**
     * Validate and update the given sacre's information.
     *
     * @param  mixed  $sacre
     * @param  array  $input
     * @return void
     */
    public function update($sacre, array $input)
    {

        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateSacreInformation');

        $sacre->fill([
            'region_id' => $input['region_id'],
            'title' => $input['title'],
            'member' => $input['member'],
            'past_member' => $input['past_member'],
            'address' => $input['address'],
            'short_code' => $input['short_code'],
            'code' => $input['code'],
            'po' => $input['po'],
            'finance' => $input['finance'],
            'virtual_training' => $input['virtual_training'],
        ])->save();
    }
}
