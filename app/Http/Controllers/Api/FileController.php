<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sacre;
use App\Models\SacreFile;
use Illuminate\Http\Request;

class FileController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param \App\Models\Sacre $sacre
     * @param \App\Models\SacreFile $file
     * @return \Illuminate\Http\Response
     */
    public function show(Sacre $sacre, SacreFile $file)
    {
        //
        $file = storage_path('app/public/files').'/' .$file->filename;
        if (file_exists($file)) {
            $headers = [
                'Content-Type' => 'application/pdf'
            ];
            return response()->file($file, $headers);
        } else {
            abort(404, 'File not found!');
        }
    }


}
