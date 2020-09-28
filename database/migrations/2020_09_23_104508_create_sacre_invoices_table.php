<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSacreInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sacre_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->index();
            $table->foreignId('sacre_id')->index();
            $table->string('po_number')->nullable($value = true);
            $table->string('address')->nullable($value = true);
            $table->timestamp('date')->nullable($value = true);
            $table->string('po_number')->nullable($value = true);
            $table->timestamps();
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
        Schema::dropIfExists('sacre_invoices');
    }
}
