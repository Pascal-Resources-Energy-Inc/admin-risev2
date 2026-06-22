<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('ad_purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('po_number')->unique();
            $table->unsignedInteger('ad_id')->nullable();
            $table->unsignedInteger('ad_user_id')->nullable();
            $table->string('business_name')->nullable();
            $table->string('authorized_territory')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email_address')->nullable();
            $table->string('shipping_type')->default('delivered');
            $table->string('payment_method')->default('voucher');
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->string('uniform_size')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->integer('total_qty')->default(0);
            $table->string('status')->default('Pending');
            $table->text('remarks')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ad_id', 'status']);
            $table->index('ad_user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_purchase_orders');
    }
}
