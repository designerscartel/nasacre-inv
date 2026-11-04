<?php

namespace App\Actions\SacreFile;

use App\Contracts\SacreFile\UpdatesSacreFile;
use App\Support\UniqueStoredFilename;
use Illuminate\Support\Facades\Validator;
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
        $filename = $input['filename'];

        if ($originalFilename != $filename) {
            $filename = UniqueStoredFilename::forDirectory('files', $filename);
            $newPath = 'files/'.$filename;

            if (! Storage::move($originalPath, $newPath)) {
                throw ValidationException::withMessages([
                    'filename' => 'The stored file could not be renamed.',
                ])->errorBag('updateFileForm');
            }
        }

        try {
            return $sacreFile->fill([
                'filename' => $filename,
                'year' => $input['year'],
            ])->save();
        } catch (\Throwable $exception) {
            if ($originalFilename != $filename) {
                Storage::move($newPath, $originalPath);
            }

            throw $exception;
        }

    }
}
