<?php

namespace App\Actions\SharedFile;

use App\Models\Sacre;
use App\Models\SharedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Contracts\SharedFile\AddsSharedFile;


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
            'file' => ['required'],
        ])->validateWithBag('addFileForm');

        if (!empty($input['file'])) {
            $myFile = $request->file('file');
            $fileName = $myFile->getClientOriginalName();
            $myFileObj = $myFile->storeAs('public/files', $fileName);
            Storage::get($myFileObj);
            $file = new SharedFile();
            $file->filename = $myFile->getClientOriginalName();
            $sacre->files()->save($file);
        }

        return true;

    }
}
