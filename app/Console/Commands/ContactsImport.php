<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Models\Sacre;
use \App\Models\SacreImports;
use \App\Models\SacreContact;

class ContactsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sacres:contactimport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Sacre contacts';

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
        $sacres = SacreImports::all();

        foreach($sacres as $sacre) {

            $sacreObject = Sacre::findOrFail($sacre->id);

            if(!empty($sacreObject)) {

                // Create Chair
                if( (!empty(trim($sacre->chair))) or (!empty(trim($sacre->chair_email))) ) {
                    $contact = new SacreContact();
                    $contact->position_id = 1;
                    $contact->name = (!empty($sacre->chair)) ? $sacre->chair : null;
                    $contact->email = (!empty($sacre->chair_email)) ? $sacre->chair_email : null;
                    $sacreObject->contacts()->save($contact);
                }

                // Create Clerk
                if( (!empty(trim($sacre->clerk))) or (!empty(trim($sacre->clerk_email))) or (!empty(trim($sacre->clerk_tel))) ) {
                    $contact = new SacreContact();
                    $contact->position_id = 2;
                    $contact->name = (!empty($sacre->clerk)) ? $sacre->clerk : null;
                    $contact->email = (!empty($sacre->clerk_email)) ? $sacre->clerk_email : null;
                    $contact->tel = (!empty($sacre->clerk_tel)) ? $sacre->clerk_tel : null;
                    $sacreObject->contacts()->save($contact);
                }

                // Create LA Support
                if( (!empty(trim($sacre->la_support))) or (!empty(trim($sacre->la_role))) or (!empty(trim($sacre->la_email))) ) {
                    $contact = new SacreContact();
                    $contact->position_id = 3;
                    $contact->name = (!empty($sacre->la_support)) ? $sacre->la_support : null;
                    $contact->role_dscpn = (!empty($sacre->la_role)) ? $sacre->la_role : null;
                    $contact->email = (!empty($sacre->la_email)) ? $sacre->la_email : null;
                    $sacreObject->contacts()->save($contact);
                }

                // Create Advisor
                if( (!empty(trim($sacre->adviser))) or (!empty(trim($sacre->adviser_email))) ) {
                    $contact = new SacreContact();
                    $contact->position_id = 4;
                    $contact->name = (!empty($sacre->adviser)) ? $sacre->adviser : null;
                    $contact->email = (!empty($sacre->adviser_email)) ? $sacre->adviser_email : null;
                    $sacreObject->contacts()->save($contact);
                }

            }

        }

        $this->info('Imported');

        return 0;
    }
}
