<?php

namespace App\Actions\SacreFile;

use App\Models\Sacre;
use App\Models\SacreFile;
use Illuminate\Support\Facades\Validator;
use App\Contracts\SacreFile\AddsSacreFile;
use Illuminate\Support\Str;


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
            'file' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:10240'],
        ])->validateWithBag('addFileForm');

        if (!empty($input['file'])) {
            $myFile = $request->file('file');
            $fileName = Str::uuid().'.pdf';
            $myFile->storeAs('files', $fileName);
            $file = new SacreFile();
            $file->filename = $fileName;
            $sacre->files()->save($file);
        }

        return true;

    }
}
