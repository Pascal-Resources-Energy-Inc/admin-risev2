<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryFeeToOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('order_details', 'delivery_fee')) {
                $table->decimal('delivery_fee', 10, 2)->nullable()->after('delivery_type');
            }
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'delivery_fee')) {
                $table->dropColumn('delivery_fee');
            }
        });
    }
}
