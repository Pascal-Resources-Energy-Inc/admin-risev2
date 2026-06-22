<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusDocumentFieldsToAdPurchaseOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_purchase_orders', 'so_number')) {
                $table->string('so_number')->nullable()->after('reference_no');
            }

            if (!Schema::hasColumn('ad_purchase_orders', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('so_number');
            }

            if (!Schema::hasColumn('ad_purchase_orders', 'dr_number')) {
                $table->string('dr_number')->nullable()->after('delivery_date');
            }

            if (!Schema::hasColumn('ad_purchase_orders', 'si_number')) {
                $table->string('si_number')->nullable()->after('dr_number');
            }
        });
    }

    public function down()
    {
        Schema::table('ad_purchase_orders', function (Blueprint $table) {
            foreach (['si_number', 'dr_number', 'delivery_date', 'so_number'] as $column) {
                if (Schema::hasColumn('ad_purchase_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
