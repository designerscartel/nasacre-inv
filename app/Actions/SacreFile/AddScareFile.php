<?php

namespace App\Actions\SacreFile;

use App\Models\Sacre;
use App\Models\SacreFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Contracts\SacreFile\AddsSacreFile;


class AddScareFile implements AddsSacreFile
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
            $file = new SacreFile();
            $file->filename = $myFile->getClientOriginalName();
            $sacre->files()->save($file);
        }

        return true;

    }
}
