<?php

namespace App\Actions\SharedFile;

use App\Contracts\SharedFile\UpdatesSharedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class UpdateSharedFile implements UpdatesSharedFile
{
    /**
     * Validate and Update the given sacre's file information.
     *
     * @param mixed $SharedFile
     * @param array $input
     * @return void
     */
    public function update($SharedFile, $sacre, array $input)
    {

        Validator::make($input, [
            'filename' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateFileForm');

        if($SharedFile->filename != $input['filename']) {
            Storage::move('public/files/'.$SharedFile->filename, 'public/files/'.$input['filename']);
        }

        return $SharedFile->fill([
            'filename' => $input['filename'],
        ])->save();

    }
}
