<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaToCenterChiefsTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('center_chiefs', 'area')) {
            Schema::table('center_chiefs', function (Blueprint $table) {
                $table->string('area')->nullable()->after('location_region');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('center_chiefs', 'area')) {
            Schema::table('center_chiefs', function (Blueprint $table) {
                $table->dropColumn('area');
            });
        }
    }
}
