<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaToAreaDistributorsTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('area_distributors', 'area')) {
            Schema::table('area_distributors', function (Blueprint $table) {
                $table->string('area')->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('area_distributors', 'area')) {
            Schema::table('area_distributors', function (Blueprint $table) {
                $table->dropColumn('area');
            });
        }
    }
}
