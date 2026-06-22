<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdPurchaseOrderPartialReceiptsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ad_purchase_order_partial_receipts')) {
            return;
        }

        Schema::create('ad_purchase_order_partial_receipts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ad_purchase_order_id');
            $table->unsignedInteger('ad_purchase_order_item_id');
            $table->date('delivery_date');
            $table->string('dr_number');
            $table->unsignedInteger('received_qty')->default(0);
            $table->unsignedInteger('confirmed_qty')->default(0);
            $table->string('status')->default('Pending');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('confirmed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index('ad_purchase_order_id', 'adpo_receipts_order_idx');
            $table->index('ad_purchase_order_item_id', 'adpo_receipts_item_idx');
            $table->index('dr_number', 'adpo_receipts_dr_idx');
            $table->index('status', 'adpo_receipts_status_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_purchase_order_partial_receipts');
    }
}
