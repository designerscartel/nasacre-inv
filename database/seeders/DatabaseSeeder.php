<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (env('SEED_ADMIN_EMAIL') && env('SEED_ADMIN_PASSWORD')) {
            $this->call(UsersTableSeeder::class);
        }
    }
}
