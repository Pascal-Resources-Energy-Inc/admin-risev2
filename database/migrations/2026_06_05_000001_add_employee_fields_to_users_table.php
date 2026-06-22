<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmployeeFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('designation')->nullable()->after('delivery_address');
            $table->string('employee_number')->nullable()->after('designation');
            $table->string('department')->nullable()->after('employee_number');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['designation', 'employee_number', 'department']);
        });
    }
}
