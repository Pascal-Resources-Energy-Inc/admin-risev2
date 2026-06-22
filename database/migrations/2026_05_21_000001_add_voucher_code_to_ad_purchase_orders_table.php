<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVoucherCodeToAdPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            $table->string('voucher_code')->nullable()->after('payment_method');
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            $table->dropColumn('voucher_code');
        });
    }
}
