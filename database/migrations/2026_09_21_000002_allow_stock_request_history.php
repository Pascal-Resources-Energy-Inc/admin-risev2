<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowStockRequestHistory extends Migration
{
    public function up()
    {
        Schema::table('dealer_stock_requests', function (Blueprint $table) {
            $table->dropUnique('dealer_stock_requests_dealer_id_product_id_unique');
            $table->index(['dealer_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::table('dealer_stock_requests', function (Blueprint $table) {
            $table->dropIndex(['dealer_id', 'product_id']);
            $table->unique(['dealer_id', 'product_id']);
        });
    }
}
