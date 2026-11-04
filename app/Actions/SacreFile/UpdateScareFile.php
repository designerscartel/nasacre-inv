<?php

namespace App\Actions\SacreFile;

use App\Contracts\SacreFile\UpdatesSacreFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
            'filename' => ['required', 'string', 'max:255', 'regex:/\\A[a-zA-Z0-9._() -]+\\.pdf\\z/i'],
            'year' => ['required', 'max:255'],
        ])->validateWithBag('updateFileForm');

        $originalFilename = $sacreFile->filename;
        $originalPath = 'files/'.$originalFilename;
        $newPath = 'files/'.$input['filename'];

        if ($sacreFile->filename != $input['filename']) {
            if (Storage::exists($newPath)) {
                throw ValidationException::withMessages([
                    'filename' => 'A file with this name already exists.',
                ])->errorBag('updateFileForm');
            }

            if (! Storage::move($originalPath, $newPath)) {
                throw ValidationException::withMessages([
                    'filename' => 'The stored file could not be renamed.',
                ])->errorBag('updateFileForm');
            }
        }

        try {
            return $sacreFile->fill([
                'filename' => $input['filename'],
                'year' => $input['year'],
            ])->save();
        } catch (\Throwable $exception) {
            if ($originalFilename != $input['filename']) {
                Storage::move($newPath, $originalPath);
            }

            throw $exception;
        }

    }
}
