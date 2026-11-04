<?php

namespace App\Actions\SharedFile;

use App\Contracts\SharedFile\UpdatesSharedFile;
use App\Support\UniqueStoredFilename;
use Illuminate\Support\Facades\Validator;
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
            return $SharedFile->fill([
                'filename' => $filename,
            ])->save();
        } catch (\Throwable $exception) {
            if ($originalFilename != $filename) {
                Storage::move($newPath, $originalPath);
            }

            throw $exception;
        }

    }
}
