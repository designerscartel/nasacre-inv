<?php

namespace App\Actions\Sacre;

use App\Models\Sacre;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Sacre\CreatesSacreInformation;

class CreateSacreInformation implements CreatesSacreInformation
{
    /**
     * Validate and Create the given sacre's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createSacreInformation');

        $sacre = new Sacre();

        return $sacre->create([
            'region_id' => $input['region_id'],
            'title' => $input['title'],
            'member' => $input['member'],
            'address' => $input['address'],
            'short_code' => $input['short_code'],
            'code' => $input['code'],
            'po' => $input['po'],
            'finance' => $input['finance'],
        ]);

    }
}
