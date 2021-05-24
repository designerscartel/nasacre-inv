<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use \App\Models\Sacre;
use \App\Models\SacreFile;
use \App\Models\Media;

class ReportFIle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sacres:reportfile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Sacre report files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $media = Media::all();
        $sacres = Sacre::all();


        foreach ($media as $file) {

            /*
             * Create new names for the files
             *
             * $name = preg_replace('/\w{1}-\w{2}-/', '$1', $file->filename, 1);
             * $file->filename = $name;
             * $file->save();
             * $this->info($name);
            */

            /*
             * Move and rename the files
             *
            if (Storage::exists('public/existing/'.$file->original)) {
                // ...
                if (!Storage::exists('public/files/'.$file->filename)) {
                    $this->info($file->filename.' moved');
                    Storage::move('public/existing/' . $file->original, 'public/files/' . $file->filename);
                } else {
                    $this->info($file->filename.' already exists');
                }
            } else {
                $this->info($file->original.' doesn\'t exists');
            }
            */

            /*
             * See if the sacre exists as best guess and attach file to it
             */
            if($file->moved != 1) {
                $name = preg_replace('/\w{4}-\w{2}-/', '$1', $file->filename, 1);
                $filecheck = str_replace(['.pdf', ','], [''], $name);
                $filecheck = trim(preg_replace('/[0-9]+/', '', $filecheck));

                foreach ($sacres as $sacre) {
                    if (stripos($sacre->title, $filecheck) !== false) {
                        $this->info($sacre->title . ' --> ' . $filecheck);
                        $sacreFile = new SacreFile();
                        $sacreFile->sacre_id = $sacre->id;
                        $sacreFile->filename = $file->filename;
                        $sacreFile->save();
                        $file->moved = 1;
                        $file->save();
                    }
                }
            }

        }

    }
}
