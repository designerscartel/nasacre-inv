<?php

namespace App\Actions\Sacre;

use App\Contracts\Sacre\RestoresSacre;
use App\Models\SacreContact;
use App\Models\SacreFile;
use App\Models\SharedFile;
use Illuminate\Support\Facades\DB;

class RestoreSacre implements RestoresSacre
{
    public function restore($sacre)
    {
        DB::transaction(function () use ($sacre) {
            $sacre->restore();
            SacreContact::onlyTrashed()->where('sacre_id', $sacre->id)->restore();
            SacreFile::onlyTrashed()->where('sacre_id', $sacre->id)->restore();
            SharedFile::onlyTrashed()->where('sacre_id', $sacre->id)->restore();
        });
    }
}
