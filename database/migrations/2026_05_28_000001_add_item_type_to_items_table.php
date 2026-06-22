<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemTypeToItemsTable extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'item_type')) {
                $table->string('item_type')->default('product')->after('for_ad');
            }
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'item_type')) {
                $table->dropColumn('item_type');
            }
        });
    }
}
