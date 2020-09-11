<?php

namespace App\Actions\Region;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Contracts\Region\UpdatesRegionInformation;

class UpdateRegionInformation implements UpdatesRegionInformation
{
    /**
     * Validate and update the given region's information.
     *
     * @param  mixed  $region
     * @param  array  $input
     * @return void
     */
    public function update($region, array $input)
    {
        Validator::make($input, [
            'title' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateRegionInformation');

        $region->fill([
            'title' => $input['title'],
        ])->save();
    }
}
