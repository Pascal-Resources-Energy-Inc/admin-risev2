<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVoucherIdToAdPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_purchase_orders', 'voucher_id')) {
                $table->unsignedInteger('voucher_id')->nullable()->after('payment_method');
                $table->index('voucher_id');
            }
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('ad_purchase_orders', 'voucher_id')) {
                $table->dropIndex(['voucher_id']);
                $table->dropColumn('voucher_id');
            }
        });
    }
}
