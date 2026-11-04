<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class UniqueStoredFilename
{
    public static function forDirectory($directory, $filename)
    {
        if (! Storage::exists($directory.'/'.$filename)) {
            return $filename;
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $suffix = 2;

        do {
            $numberedSuffix = '-'.$suffix;
            $maximumNameLength = 255 - strlen($extension) - strlen($numberedSuffix) - 1;
            $candidate = substr($name, 0, $maximumNameLength).$numberedSuffix.'.'.$extension;
            $suffix++;
        } while (Storage::exists($directory.'/'.$candidate));

        return $candidate;
    }
}
