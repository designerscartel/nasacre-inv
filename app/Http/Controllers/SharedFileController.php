<?php

namespace App\Http\Controllers;

use App\Contracts\SharedFile\UpdatesSharedFile;
use App\Contracts\SharedFile\AddsSharedFile;
use App\Actions\SharedFile\DeleteSharedFile;
use App\Models\Sacre;
use App\Models\SharedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class SharedFileController extends Controller
{

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


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sacre $sacre
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Sacre $sacre)
    {
        //
        app(AddsSharedFile::class)->add($sacre, $request);

        return back(303);
    }


    /**
     * Update the given files details.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sacre $sacre
     * @param \App\Models\SharedFile $file
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Sacre $sacre, SharedFile $file)
    {
        //
        app(UpdatesSharedFile::class)->update($file, $sacre, $request->all());

        return back(303);
    }

    /**
     * Delete the current file.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Sacre $sacre
     * @param \App\Models\SharedFile $file
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Sacre $sacre, SharedFile $file)
    {
        //
        app(DeleteSharedFile::class)->delete($file);

        return back(303);
    }


}
