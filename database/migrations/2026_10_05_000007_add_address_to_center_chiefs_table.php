<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressToCenterChiefsTable extends Migration
{
    public function up()
    {
        Schema::table('center_chiefs', function (Blueprint $table) {
            $table->string('street_address')->nullable()->after('number');
            $table->string('location_barangay')->nullable()->after('street_address');
            $table->string('location_city')->nullable()->after('location_barangay');
            $table->string('location_province')->nullable()->after('location_city');
            $table->string('location_region')->nullable()->after('location_province');
        });
    }

    public function down()
    {
        Schema::table('center_chiefs', function (Blueprint $table) {
            $table->dropColumn(['street_address', 'location_barangay', 'location_city', 'location_province', 'location_region']);
        });
    }
}
