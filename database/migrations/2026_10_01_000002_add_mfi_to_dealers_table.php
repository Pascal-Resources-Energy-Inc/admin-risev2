<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMfiToDealersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('dealers', 'mfi')) {
            Schema::table('dealers', function (Blueprint $table) {
                $table->string('mfi')->nullable()->after('area');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('dealers', 'mfi')) {
            Schema::table('dealers', function (Blueprint $table) {
                $table->dropColumn('mfi');
            });
        }
    }
}
