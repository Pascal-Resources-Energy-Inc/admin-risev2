<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSizeBreakdownToAdPurchaseOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_purchase_order_items', 'size_breakdown')) {
                $table->text('size_breakdown')->nullable()->after('color_breakdown');
            }
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('ad_purchase_order_items', 'size_breakdown')) {
                $table->dropColumn('size_breakdown');
            }
        });
    }
}
