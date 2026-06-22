<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankNameToAdPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('payment_method');
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            $table->dropColumn('bank_name');
        });
    }
}
