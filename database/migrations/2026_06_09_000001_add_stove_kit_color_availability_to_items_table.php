<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoveKitColorAvailabilityToItemsTable extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'stove_kit_color_availability')) {
                $afterColumn = Schema::hasColumn('items', 'item_type') ? 'item_type' : 'for_ad';
                $table->text('stove_kit_color_availability')->nullable()->after($afterColumn);
            }
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'stove_kit_color_availability')) {
                $table->dropColumn('stove_kit_color_availability');
            }
        });
    }
}
