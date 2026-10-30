<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReconcileApplicationSchema extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->string('email');
                $table->decimal('subscribed', 11, 2);
                $table->decimal('none_subscribed', 11, 2);
                $table->decimal('additional_amount', 11, 2)->nullable();
                $table->timestamp('date')->nullable();
                $table->integer('year');
                $table->string('from');
                $table->text('message');
                $table->text('venue')->nullable();
                $table->integer('batch')->default(0);
                $table->boolean('new')->default(false);
                $table->boolean('active')->default(false)->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('booking_subs')) {
            Schema::create('booking_subs', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('booking_id')->nullable()->index();
                $table->decimal('sub', 11, 2)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('booking_member_subs')) {
            Schema::create('booking_member_subs', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('booking_id')->nullable()->index();
                $table->decimal('sub', 11, 2)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('sacre_bookings')) {
            Schema::create('sacre_bookings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('booking_id')->index();
                $table->unsignedBigInteger('sacre_id')->index();
                $table->string('po_number')->nullable();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->timestamp('date')->nullable();
                $table->string('delegate_one_name')->nullable();
                $table->string('delegate_one_email')->nullable();
                $table->string('delegate_one_diet')->nullable();
                $table->boolean('delegate_one_free')->nullable();
                $table->string('delegate_two_name')->nullable();
                $table->string('delegate_two_email')->nullable();
                $table->string('delegate_two_diet')->nullable();
                $table->boolean('delegate_two_free')->nullable();
                $table->string('virtual_one_name')->nullable();
                $table->string('virtual_one_email')->nullable();
                $table->boolean('virtual_one_free')->nullable();
                $table->string('virtual_two_name')->nullable();
                $table->string('virtual_two_email')->nullable();
                $table->boolean('virtual_two_free')->nullable();
                $table->boolean('confirmed')->nullable();
                $table->text('address')->nullable();
                $table->boolean('offer')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('booking_delegates')) {
            Schema::create('booking_delegates', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('sacre_booking_id')->nullable()->index();
                $table->unsignedBigInteger('booking_id')->nullable()->index();
                $table->unsignedBigInteger('sacre_id')->nullable()->index();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('diet')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('sacre_files')) {
            Schema::create('sacre_files', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sacre_id')->index();
                $table->string('filename')->default('');
                $table->string('year')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('shared_files')) {
            Schema::create('shared_files', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sacre_id')->index();
                $table->string('filename')->default('');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('media')) {
            Schema::create('media', function (Blueprint $table) {
                $table->increments('id');
                $table->string('filename')->index();
                $table->string('original');
                $table->boolean('moved')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasColumn('sacres', 'past_member')) {
            Schema::table('sacres', function (Blueprint $table) {
                $table->boolean('past_member')->nullable()->after('member');
            });
        }

        if (! Schema::hasColumn('sacres', 'virtual_training')) {
            Schema::table('sacres', function (Blueprint $table) {
                $table->boolean('virtual_training')->nullable()->after('finance');
            });
        }

        if (! Schema::hasColumn('sacre_invoices', 'virtual_training')) {
            Schema::table('sacre_invoices', function (Blueprint $table) {
                $table->boolean('virtual_training')->nullable()->after('po_number');
            });
        }
    }

    public function down()
    {
    }
}
