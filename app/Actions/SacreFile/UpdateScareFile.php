<?php

namespace App\Actions\SacreFile;

use App\Contracts\SacreFile\UpdatesSacreFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class UpdateScareFile implements UpdatesSacreFile
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
            'year' => ['required', 'max:255'],
        ])->validateWithBag('updateFileForm');

        if($sacreFile->filename != $input['filename']) {
            Storage::move('public/files/'.$sacreFile->filename, 'public/files/'.$input['filename']);
        }

        return $sacreFile->fill([
            'filename' => $input['filename'],
            'year' => $input['year'],
        ])->save();

    }
}
