<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTransfersTable extends Migration
{
    public function up()
    {
        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ad_id')->nullable();
            $table->unsignedInteger('ad_user_id');
            $table->unsignedInteger('product_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('item_name');
            $table->enum('movement_type', ['in', 'out', 'transfer']);
            $table->string('from_area')->nullable();
            $table->string('to_area')->nullable();
            $table->integer('qty')->default(0);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('reference_no')->nullable();
            $table->date('transfer_date')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ad_user_id', 'movement_type']);
            $table->index(['from_area', 'to_area']);
            $table->index('product_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_transfers');
    }
}
