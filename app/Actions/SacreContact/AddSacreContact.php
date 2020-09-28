<?php

namespace App\Actions\SacreContact;

use App\Models\Sacre;
use App\Models\SacreContact;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\SacreContact\AddsSacreContact;

class AddSacreContact implements AddsSacreContact
{
    /**
     * Validate and Create the given sacre's contact information.
     *
     * @param mixed $sacre
     * @param array $input
     * @return void
     */
    public function add($sacre, array $input)
    {
        Validator::make($input, [
            'position_id' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'tel' => ['required', 'string', 'max:255'],
            'role_dscpn' => [],
            'default' => [],
        ])->validateWithBag('addContactForm');

        if($input['default']) {
            $sacre->contacts()->update(['default' => 0]);
        }

        $contact = new SacreContact();
        $contact->position_id = $input['position_id'];
        $contact->name = $input['name'];
        $contact->email = $input['email'];
        $contact->tel = $input['tel'];
        $contact->role_dscpn = $input['role_dscpn'];
        $contact->default = $input['default'];

        return $sacre->contacts()->save($contact);

    }
}
