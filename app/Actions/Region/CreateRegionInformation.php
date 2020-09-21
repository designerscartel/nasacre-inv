<?php

namespace App\Actions\Region;

use App\Models\Region;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Region\CreatesRegionInformation;

class CreateRegionInformation implements CreatesRegionInformation
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
        ])->validateWithBag('createRegionInformation');

        $region = new Region();

        return $region->create([
            'title' => $input['title'],
        ]);

    }
}
