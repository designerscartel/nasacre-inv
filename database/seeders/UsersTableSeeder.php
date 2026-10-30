<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->updateOrInsert([
            'email' => env('SEED_ADMIN_EMAIL'),
        ], [
            'name' => env('SEED_ADMIN_NAME', 'Administrator'),
            'password' => bcrypt(env('SEED_ADMIN_PASSWORD')),
            'email_verified_at' => now(),
            'updated_at' => now(),
            'created_at' => now(),
        ]);
    }
}
