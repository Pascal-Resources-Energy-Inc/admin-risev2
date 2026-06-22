<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdPurchaseOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('ad_purchase_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ad_purchase_order_id');
            $table->unsignedInteger('product_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->string('product_image')->nullable();
            $table->integer('qty')->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->decimal('dealer_points', 12, 2)->default(0);
            $table->timestamps();

            $table->index('ad_purchase_order_id');
            $table->index('product_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_purchase_order_items');
    }
}
