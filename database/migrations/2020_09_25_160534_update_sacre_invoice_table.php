<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSacreInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('invoices', function($table)
        {
            $table->integer('batch')->default(0);
        });

        Schema::table('sacre_invoices', function($table)
        {
            $table->integer('batch')->default(0);
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
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('batch');
        });

        Schema::table('sacre_invoices', function($table)
        {
            $table->dropColumn('batch');
        });
    }
}
