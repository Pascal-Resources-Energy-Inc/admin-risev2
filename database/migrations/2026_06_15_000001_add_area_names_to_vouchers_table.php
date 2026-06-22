<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaNamesToVouchersTable extends Migration
{
    public function up()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->text('area_names')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('area_names');
        });
    }
}
