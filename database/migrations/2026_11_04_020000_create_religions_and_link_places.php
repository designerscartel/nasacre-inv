<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('religions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('places_of_interest', function (Blueprint $table) {
            $table->foreignId('religion_id')->nullable()->after('name')->constrained()->nullOnDelete();
        });

        DB::table('places_of_interest')
            ->whereNotNull('religion')
            ->where('religion', '<>', '')
            ->select('religion')
            ->distinct()
            ->orderBy('religion')
            ->each(function ($place) {
                $religionId = DB::table('religions')->insertGetId([
                    'name' => $place->religion,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('places_of_interest')
                    ->where('religion', $place->religion)
                    ->update(['religion_id' => $religionId]);
            });

        Schema::table('places_of_interest', function (Blueprint $table) {
            $table->dropColumn('religion');
        });
    }

    public function down(): void
    {
        Schema::table('places_of_interest', function (Blueprint $table) {
            $table->string('religion')->nullable()->after('name');
        });

        DB::table('places_of_interest')
            ->join('religions', 'religions.id', '=', 'places_of_interest.religion_id')
            ->update(['places_of_interest.religion' => DB::raw('religions.name')]);

        Schema::table('places_of_interest', function (Blueprint $table) {
            $table->dropConstrainedForeignId('religion_id');
        });

        Schema::dropIfExists('religions');
    }
};
