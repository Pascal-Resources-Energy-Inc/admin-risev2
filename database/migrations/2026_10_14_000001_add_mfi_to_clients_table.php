<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMfiToClientsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('clients') && !Schema::hasColumn('clients', 'mfi')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('mfi', 100)->nullable()->after('center');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('clients') && Schema::hasColumn('clients', 'mfi')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn('mfi');
            });
        }
    }
}
