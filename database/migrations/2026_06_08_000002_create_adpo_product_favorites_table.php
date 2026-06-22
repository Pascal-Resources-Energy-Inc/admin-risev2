<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdpoProductFavoritesTable extends Migration
{
    public function up()
    {
        Schema::create('adpo_product_favorites', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('item_id');
            $table->timestamps();

            $table->unique(['user_id', 'item_id']);
            $table->index('item_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('adpo_product_favorites');
    }
}
