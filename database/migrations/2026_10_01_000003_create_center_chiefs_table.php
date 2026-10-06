<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCenterChiefsTable extends Migration
{
    public function up()
    {
        Schema::create('center_chiefs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('number', 50)->nullable();
            $table->string('mfi', 100);
            $table->integer('center_id')->unique();
            $table->string('status', 20)->default('Active');
            $table->timestamps();
            $table->foreign('center_id')->references('id')->on('centers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('center_chiefs');
    }
}
