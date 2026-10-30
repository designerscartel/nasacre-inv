<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

class MoveUploadedFilesToPrivateStorage extends Migration
{
    public function up()
    {
        foreach (Storage::files('public/files') as $file) {
            Storage::move($file, 'files/'.basename($file));
        }

        Storage::deleteDirectory('public/files');
    }

    public function down()
    {
        foreach (Storage::files('files') as $file) {
            Storage::move($file, 'public/files/'.basename($file));
        }

        Storage::deleteDirectory('files');
    }
}
