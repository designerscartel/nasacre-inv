<?php

namespace App\Actions\ContactPosition;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\ContactPosition\UpdatesContactPositionInformation;

class UpdateContactPositionInformation implements UpdatesContactPositionInformation
{
    /**
     * Validate and update the given region's information.
     *
     * @param  mixed  $contactPosition
     * @param  array  $input
     * @return void
     */
    public function update($contactPosition, array $input)
    {
        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updatePositionInformation');

        $contactPosition->fill([
            'title' => $input['title'],
        ])->save();
    }
}
