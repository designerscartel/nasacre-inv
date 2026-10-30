<?php

namespace App\Actions\SharedFile;

use App\Models\Sacre;
use App\Models\SharedFile;
use Illuminate\Support\Facades\Validator;
use App\Contracts\SharedFile\AddsSharedFile;
use Illuminate\Support\Str;


class AddSharedFile implements AddsSharedFile
{
    /**
     * Validate and Create the given sacre's file information.
     *
     * @param mixed $sacre
     * @param mixed $request
     * @return boolean
     */
    public function add($sacre, $request)
    {
        $input = $request->all();

        Validator::make($input, [
            'file' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:10240'],
        ])->validateWithBag('addFileForm');

        if (!empty($input['file'])) {
            $myFile = $request->file('file');
            $fileName = Str::uuid().'.pdf';
            $myFile->storeAs('files', $fileName);
            $file = new SharedFile();
            $file->filename = $fileName;
            $sacre->shared()->save($file);
        }

        return true;

    }
}
