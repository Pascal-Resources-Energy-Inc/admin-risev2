<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPartialDeliveryFieldsToAdPurchaseOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_purchase_order_items', 'partial_delivery_date')) {
                $table->date('partial_delivery_date')->nullable()->after('partial_received_qty');
            }

            if (!Schema::hasColumn('ad_purchase_order_items', 'partial_dr_number')) {
                $table->string('partial_dr_number')->nullable()->after('partial_delivery_date');
            }
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('ad_purchase_order_items', 'partial_dr_number')) {
                $table->dropColumn('partial_dr_number');
            }

            if (Schema::hasColumn('ad_purchase_order_items', 'partial_delivery_date')) {
                $table->dropColumn('partial_delivery_date');
            }
        });
    }
}
