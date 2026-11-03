<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryTrackingToCampaigns extends Migration
{
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->integer('batch')->default(0)->after('message');
        });

        Schema::create('campaign_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id')->index();
            $table->integer('batch')->index();
            $table->string('delivery_status')->default('queued')->index();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email');
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('campaign_deliveries');

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('batch');
        });
    }
}
