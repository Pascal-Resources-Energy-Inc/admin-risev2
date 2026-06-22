<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWarehouseAndDeliveryAddressToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'warehouse')) {
                $table->string('warehouse')->nullable()->after('address');
            }

            if (!Schema::hasColumn('users', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('warehouse');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'delivery_address')) {
                $table->dropColumn('delivery_address');
            }

            if (Schema::hasColumn('users', 'warehouse')) {
                $table->dropColumn('warehouse');
            }
        });
    }
}
