<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProofOfPaymentToAdPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            $table->string('proof_of_payment')->nullable()->after('reference_no');
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            $table->dropColumn('proof_of_payment');
        });
    }
}
