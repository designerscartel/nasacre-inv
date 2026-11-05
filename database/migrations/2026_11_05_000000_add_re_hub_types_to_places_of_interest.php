<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('places_of_interest', function (Blueprint $table) {
            $table->boolean('is_place_of_interest')->default(false)->after('name');
            $table->boolean('is_school_speaker')->default(false)->after('is_place_of_interest');
        });

        DB::table('places_of_interest')->update(['is_place_of_interest' => true]);
    }

    public function down(): void
    {
        Schema::table('places_of_interest', function (Blueprint $table) {
            $table->dropColumn(['is_place_of_interest', 'is_school_speaker']);
        });
    }
};
