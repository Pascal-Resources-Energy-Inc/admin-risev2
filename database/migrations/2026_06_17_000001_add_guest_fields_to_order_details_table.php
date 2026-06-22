<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuestFieldsToOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('order_details', 'is_guest')) {
                $table->boolean('is_guest')->default(false)->after('dealer_id');
            }

            if (!Schema::hasColumn('order_details', 'guest_name')) {
                $table->string('guest_name')->nullable()->after('is_guest');
            }

            if (!Schema::hasColumn('order_details', 'guest_email')) {
                $table->string('guest_email')->nullable()->after('guest_name');
            }

            if (!Schema::hasColumn('order_details', 'guest_phone')) {
                $table->string('guest_phone', 40)->nullable()->after('guest_email');
            }

            if (!Schema::hasColumn('order_details', 'guest_address')) {
                $table->text('guest_address')->nullable()->after('guest_phone');
            }

            if (!Schema::hasColumn('order_details', 'guest_notes')) {
                $table->text('guest_notes')->nullable()->after('guest_address');
            }
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            foreach (['guest_notes', 'guest_address', 'guest_phone', 'guest_email', 'guest_name', 'is_guest'] as $column) {
                if (Schema::hasColumn('order_details', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
