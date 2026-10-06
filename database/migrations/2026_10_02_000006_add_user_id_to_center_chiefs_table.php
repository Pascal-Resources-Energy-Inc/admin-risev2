<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUserIdToCenterChiefsTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('center_chiefs', 'user_id')) {
            Schema::table('center_chiefs', function (Blueprint $table) {
                $table->unsignedInteger('user_id')->nullable()->unique()->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        DB::table('center_chiefs')->whereNotNull('email')->orderBy('id')->get()->each(function ($chief) {
            $userId = DB::table('users')->where('email', $chief->email)->value('id');
            if ($userId) { DB::table('center_chiefs')->where('id', $chief->id)->update(['user_id' => $userId]); }
        });
    }

    public function down()
    {
        // The column may have existed before this feature was installed, so it is retained on rollback.
    }
}
