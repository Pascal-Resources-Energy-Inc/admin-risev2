<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRaffleEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('raffle_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('raffle_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('participant_name');
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('participant_key');
            $table->string('ticket_number')->unique();
            $table->string('status')->default('eligible');
            $table->dateTime('entered_at');
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('raffle_id')->references('id')->on('raffles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['raffle_id', 'participant_key']);
            $table->index(['raffle_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('raffle_entries');
    }
}
