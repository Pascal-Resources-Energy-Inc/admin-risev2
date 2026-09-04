<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaToClientsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('clients') && !Schema::hasColumn('clients', 'area')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('area')->nullable()->after('center');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('clients') && Schema::hasColumn('clients', 'area')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('area');
            });
        }
    }
}
