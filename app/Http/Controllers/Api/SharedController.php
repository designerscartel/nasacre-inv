<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiSharedResource;
use App\Models\Sacre;
use App\Models\SharedFile;
use Illuminate\Http\Request;

class SharedController extends Controller
{

    public function index(SharedFile $sharedFile)
    {
        //
        return ApiSharedResource::collection($sharedFile::all());
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Sacre $sacre
     * @param \App\Models\SharedFile $file
     * @return \Illuminate\Http\Response
     */
    public function show(Sacre $sacre, SharedFile $file)
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

    public function shared($term)
    {
        //
        $results = SharedFile::search($term)->get();
        return ApiSharedResource::collection($results);
    }





}
