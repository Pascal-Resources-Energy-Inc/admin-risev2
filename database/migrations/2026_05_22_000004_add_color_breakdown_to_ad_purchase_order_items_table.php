<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColorBreakdownToAdPurchaseOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_purchase_order_items', 'color_breakdown')) {
                $table->text('color_breakdown')->nullable()->after('product_image');
            }
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('ad_purchase_order_items', 'color_breakdown')) {
                $table->dropColumn('color_breakdown');
            }
        });
    }
}
