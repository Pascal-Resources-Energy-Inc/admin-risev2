<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeliveryAddressToAreaDistributorsTable extends Migration
{
    public function up()
    {
        Schema::connection('dms')->table('area_distributors', function (Blueprint $table) {
            if (!Schema::connection('dms')->hasColumn('area_distributors', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('address');
            }
        });
    }

    public function down()
    {
        Schema::connection('dms')->table('area_distributors', function (Blueprint $table) {
            if (Schema::connection('dms')->hasColumn('area_distributors', 'delivery_address')) {
                $table->dropColumn('delivery_address');
            }
        });
    }
}
