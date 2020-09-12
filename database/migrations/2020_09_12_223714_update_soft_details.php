<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSoftDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('sacres', function($table)
        {
            $table->softDeletes();
        });

        Schema::table('regions', function($table)
        {
            $table->softDeletes();
        });

        Schema::table('sacre_contacts', function($table)
        {
            $table->softDeletes();
        });

        Schema::table('contact_positions', function($table)
        {
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('sacres', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('sacre_contacts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('contact_positions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

    }
}
