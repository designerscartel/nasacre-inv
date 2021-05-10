<?php

namespace App\Actions\SacreFile;

use App\Contracts\SacreFile\UpdatesSacreFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateSacreFile implements UpdatesSacreFile
{
    /**
     * Validate and Update the given sacre's file information.
     *
     * @param mixed $sacreFile
     * @param array $input
     * @return void
     */
    public function update($sacreFile, $sacre, array $input)
    {
        Validator::make($input, [
            'filename' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateFileForm');

        return $sacreFile->fill([
            'filename' => $input['filename'],
        ])->save();

    }
}
