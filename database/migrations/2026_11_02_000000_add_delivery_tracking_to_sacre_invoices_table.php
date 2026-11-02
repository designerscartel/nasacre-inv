<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryTrackingToSacreInvoicesTable extends Migration
{
    public function up()
    {
        Schema::table('sacre_invoices', function (Blueprint $table) {
            $table->string('delivery_status')->default('sent')->after('batch')->index();
            $table->string('recipient_name')->nullable()->after('delivery_status');
            $table->string('recipient_email')->nullable()->after('recipient_name');
            $table->timestamp('queued_at')->nullable()->after('recipient_email');
            $table->timestamp('sent_at')->nullable()->after('queued_at');
            $table->timestamp('failed_at')->nullable()->after('sent_at');
            $table->text('failure_message')->nullable()->after('failed_at');
        });
    }

    public function down()
    {
        Schema::table('sacre_invoices', function (Blueprint $table) {
            $table->dropIndex(['delivery_status']);
            $table->dropColumn([
                'delivery_status',
                'recipient_name',
                'recipient_email',
                'queued_at',
                'sent_at',
                'failed_at',
                'failure_message',
            ]);
        });
    }
}
