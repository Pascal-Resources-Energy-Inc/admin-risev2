<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRebateAmountToAdPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_purchase_orders', 'rebate_amount')) {
                $table->decimal('rebate_amount', 12, 2)->default(0)->after('voucher_code');
            }
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('ad_purchase_orders', 'rebate_amount')) {
                $table->dropColumn('rebate_amount');
            }
        });
    }
}
