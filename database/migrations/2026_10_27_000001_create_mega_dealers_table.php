<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMegaDealersTable extends Migration
{
    public function up()
    {
        if (Schema::connection('mysql')->hasTable('mega_dealers')) {
            return;
        }

        Schema::connection('mysql')->create('mega_dealers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->unique();
            $table->string('partner_code', 32)->unique();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('mothers_name')->nullable();
            $table->date('birthdate')->nullable();
            $table->unsignedSmallInteger('age')->nullable();
            $table->string('email_address')->nullable();
            $table->string('number', 50)->nullable();
            $table->string('facebook')->nullable();
            $table->string('store_name')->nullable();
            $table->string('store_type')->nullable();
            $table->string('mfi', 100)->nullable();
            $table->string('center')->nullable();
            $table->string('spo')->nullable();
            $table->string('area')->nullable();
            $table->text('address')->nullable();
            $table->string('street_address')->nullable();
            $table->string('location_region')->nullable();
            $table->string('location_province')->nullable();
            $table->string('location_city')->nullable();
            $table->string('location_barangay')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status', 30)->default('Active');
            $table->string('avatar')->nullable();
            $table->string('contract')->nullable();
            $table->string('valid_file')->nullable();
            $table->string('valid_id')->nullable();
            $table->string('valid_id_number')->nullable();
            $table->string('signature')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'name']);
        });
    }

    public function down()
    {
        Schema::connection('mysql')->dropIfExists('mega_dealers');
    }
}
