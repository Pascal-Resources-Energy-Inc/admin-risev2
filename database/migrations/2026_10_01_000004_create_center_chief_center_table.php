<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCenterChiefCenterTable extends Migration
{
    public function up()
    {
        Schema::create('center_chief_center', function (Blueprint $table) {
            $table->unsignedBigInteger('center_chief_id');
            $table->integer('center_id');
            $table->timestamps();
            $table->unique(['center_chief_id', 'center_id']);
            $table->unique('center_id');
            $table->foreign('center_chief_id')->references('id')->on('center_chiefs')->onDelete('cascade');
            $table->foreign('center_id')->references('id')->on('centers')->onDelete('cascade');
        });

        DB::table('center_chiefs')->whereNotNull('center_id')->orderBy('id')->get()->each(function ($chief) {
            DB::table('center_chief_center')->insert([
                'center_chief_id' => $chief->id,
                'center_id' => $chief->center_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        DB::statement('ALTER TABLE `center_chiefs` DROP FOREIGN KEY `center_chiefs_center_id_foreign`');
        DB::statement('ALTER TABLE `center_chiefs` DROP INDEX `center_chiefs_center_id_unique`');
        DB::statement('ALTER TABLE `center_chiefs` CHANGE `center_id` `center_id` INT NULL DEFAULT NULL');
    }

    public function down()
    {
        DB::table('center_chiefs')->orderBy('id')->get()->each(function ($chief) {
            $centerId = DB::table('center_chief_center')->where('center_chief_id', $chief->id)->value('center_id');
            if ($centerId) {
                DB::table('center_chiefs')->where('id', $chief->id)->update(['center_id' => $centerId]);
            }
        });

        Schema::dropIfExists('center_chief_center');
        DB::statement('ALTER TABLE `center_chiefs` CHANGE `center_id` `center_id` INT NOT NULL');
        DB::statement('ALTER TABLE `center_chiefs` ADD UNIQUE `center_chiefs_center_id_unique` (`center_id`)');
        DB::statement('ALTER TABLE `center_chiefs` ADD CONSTRAINT `center_chiefs_center_id_foreign` FOREIGN KEY (`center_id`) REFERENCES `centers` (`id`) ON DELETE CASCADE');
    }
}
