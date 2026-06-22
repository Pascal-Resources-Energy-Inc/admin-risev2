<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuestAuthorizedTerritoryToOrderDetailsTable extends Migration
{
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (!Schema::hasColumn('order_details', 'guest_authorized_territory')) {
                $table->string('guest_authorized_territory')->nullable()->after('guest_phone');
            }
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            if (Schema::hasColumn('order_details', 'guest_authorized_territory')) {
                $table->dropColumn('guest_authorized_territory');
            }
        });
    }
}
