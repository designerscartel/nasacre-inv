<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalOverrideToSacreInvoicesTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('sacre_invoices', 'total_override')) {
            Schema::table('sacre_invoices', function (Blueprint $table) {
                $table->decimal('total_override', 11, 2)->nullable()->after('virtual_training');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('sacre_invoices', 'total_override')) {
            Schema::table('sacre_invoices', function (Blueprint $table) {
                $table->dropColumn('total_override');
            });
        }
    }
}
