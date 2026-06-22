<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeliveryAddressToAdPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_purchase_orders', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('email_address');
            }
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('ad_purchase_orders', 'delivery_address')) {
                $table->dropColumn('delivery_address');
            }
        });
    }
}
