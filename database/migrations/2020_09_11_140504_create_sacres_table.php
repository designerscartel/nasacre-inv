<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSacresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sacres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->index();
            $table->string('title');
            $table->boolean('member')->nullable($value = true);
            $table->text('address')->nullable($value = true);
            $table->string('short_code')->nullable($value = true);
            $table->integer('code');
            $table->string('po')->nullable($value = true);
            $table->string('finance')->nullable($value = true);
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
        Schema::dropIfExists('sacres');
    }
}
