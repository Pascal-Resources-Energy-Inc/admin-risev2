<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferenceNoToAdPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_purchase_orders', 'reference_no')) {
                $table->string('reference_no')->nullable()->after('payment_method');
            }
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('ad_purchase_orders', 'reference_no')) {
                $table->dropColumn('reference_no');
            }
        });
    }
}
