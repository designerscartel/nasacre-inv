<?php

namespace App\Actions\SacreContact;

use App\Contracts\SacreContact\UpdatesSacreContact;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateSacreContact implements UpdatesSacreContact
{
    /**
     * Validate and Update the given sacre's contact information.
     *
     * @param mixed $sacreContact
     * @param array $input
     * @return void
     */
    public function update($sacreContact, array $input)
    {
        Validator::make($input, [
            'position_id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'tel' => ['required', 'string', 'max:255'],
            'role_dscpn' => [],
            'default' => []
        ])->validateWithBag('updateContactForm');


        return $sacreContact->fill([
            'position_id' => $input['position_id'],
            'name' => $input['name'],
            'tel' => $input['tel'],
            'email' => $input['email'],
            'role_dscpn' => $input['role_dscpn'],
            'default' => $input['default']
        ])->save();

    }
}
