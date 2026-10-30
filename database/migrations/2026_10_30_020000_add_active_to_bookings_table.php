<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveToBookingsTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('bookings', 'active')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->boolean('active')->default(false)->index()->after('new');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('bookings', 'active')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('active');
            });
        }
    }
}
