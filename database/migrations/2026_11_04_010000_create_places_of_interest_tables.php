<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('place_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('places_of_interest', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('religion')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('organisation_email')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('last_training')->nullable();
            $table->string('charge_to_speak')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('do_not_list')->default(false);
            $table->string('source_key', 64)->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('place_area_place_of_interest', function (Blueprint $table) {
            $table->foreignId('place_area_id')->constrained()->cascadeOnDelete();
            $table->foreignId('place_of_interest_id')->constrained('places_of_interest')->cascadeOnDelete();
            $table->primary(['place_area_id', 'place_of_interest_id'], 'place_area_place_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('place_area_place_of_interest');
        Schema::dropIfExists('places_of_interest');
        Schema::dropIfExists('place_areas');
    }
};
