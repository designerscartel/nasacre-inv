<?php

namespace App\Actions\ContactPosition;

use App\Models\ContactPosition;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\ContactPosition\CreatesContactPositionInformation;

class CreateContactPositionInformation implements CreatesContactPositionInformation
{
    /**
     * Validate and Create the given region's information.
     *
     * @param  array  $input
     * @return void
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createPositionsInformation');

        $contactPosition = new ContactPosition();

        return $contactPosition->create([
            'title' => $input['title'],
        ]);

    }
}
