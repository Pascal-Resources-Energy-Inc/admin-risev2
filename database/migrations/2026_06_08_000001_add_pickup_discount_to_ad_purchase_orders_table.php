<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPickupDiscountToAdPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_purchase_orders', 'pickup_discount')) {
                $table->decimal('pickup_discount', 12, 2)->default(0)->after('rebate_amount');
            }
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('ad_purchase_orders', 'pickup_discount')) {
                $table->dropColumn('pickup_discount');
            }
        });
    }
}
