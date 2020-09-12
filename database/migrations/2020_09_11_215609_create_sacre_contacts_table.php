<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSacreContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sacre_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sacre_id')->index();
            $table->foreignId('position_id')->index();
            $table->string('name');
            $table->string('email')->nullable($value = true);
            $table->string('tel')->nullable($value = true);
            $table->string('role_dscpn')->nullable($value = true);
            $table->boolean('default')->nullable($value = true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sacre_contacts');
    }
}
