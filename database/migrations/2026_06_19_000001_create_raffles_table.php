<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRafflesTable extends Migration
{
    public function up()
    {
        Schema::create('raffles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('prize');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->unsignedInteger('max_entries_per_participant')->default(1);
            $table->string('status')->default('draft');
            $table->unsignedInteger('winning_entry_id')->nullable();
            $table->dateTime('drawn_at')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['status', 'starts_at', 'ends_at']);
            $table->index('winning_entry_id');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('raffles');
    }
}
