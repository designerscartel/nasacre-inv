<?php

namespace App\Actions\SharedFile;

use App\Contracts\SharedFile\UpdatesSharedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
            'filename' => ['required', 'string', 'max:255', 'regex:/\\A[a-zA-Z0-9._() -]+\\.pdf\\z/i'],
        ])->validateWithBag('updateFileForm');

        $originalFilename = $SharedFile->filename;
        $originalPath = 'files/'.$originalFilename;
        $newPath = 'files/'.$input['filename'];

        if ($SharedFile->filename != $input['filename']) {
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
            return $SharedFile->fill([
                'filename' => $input['filename'],
            ])->save();
        } catch (\Throwable $exception) {
            if ($originalFilename != $input['filename']) {
                Storage::move($newPath, $originalPath);
            }

            throw $exception;
        }

    }
}
