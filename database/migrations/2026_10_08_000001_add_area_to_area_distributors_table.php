<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaToAreaDistributorsTable extends Migration
{
    public function up()
    {
        if (! Schema::connection('dms')->hasColumn('area_distributors', 'area')) {
            Schema::connection('dms')->table('area_distributors', function (Blueprint $table) {
                $table->string('area')->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::connection('dms')->hasColumn('area_distributors', 'area')) {
            Schema::connection('dms')->table('area_distributors', function (Blueprint $table) {
                $table->dropColumn('area');
            });
        }
    }
}
