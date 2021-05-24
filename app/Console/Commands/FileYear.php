<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use \App\Models\SacreFile;

class FileYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sacres:fileyear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Year for imported file';

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
        $files = SacreFile::all();

        foreach ($files as $file) {
            $year = substr ( $file->filename , 0 ,4 );
            $yearDate = \Carbon\Carbon::parse($year.'-01-01')->toDateTimeString();
            $file->year = $yearDate;
            $file->save();
            $this->info($yearDate);
        }
    }
}
