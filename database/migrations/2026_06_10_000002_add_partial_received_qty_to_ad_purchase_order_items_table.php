<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPartialReceivedQtyToAdPurchaseOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_purchase_order_items', 'partial_received_qty')) {
                $table->unsignedInteger('partial_received_qty')->default(0)->after('qty');
            }
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('ad_purchase_order_items', 'partial_received_qty')) {
                $table->dropColumn('partial_received_qty');
            }
        });
    }
}
