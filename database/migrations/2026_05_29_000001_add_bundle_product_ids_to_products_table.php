<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBundleProductIdsToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'bundle_product_ids')) {
                $table->text('bundle_product_ids')->nullable()->after('product_image');
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'bundle_product_ids')) {
                $table->dropColumn('bundle_product_ids');
            }
        });
    }
}
