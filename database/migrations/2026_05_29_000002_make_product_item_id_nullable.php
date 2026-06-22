<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeProductItemIdNullable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('products', 'item_id')) {
            return;
        }

        $column = DB::selectOne("
            SELECT COLUMN_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'products'
                AND COLUMN_NAME = 'item_id'
        ");

        if ($column && !empty($column->COLUMN_TYPE)) {
            DB::statement("ALTER TABLE products MODIFY item_id {$column->COLUMN_TYPE} NULL");
        }
    }

    public function down()
    {
        //
    }
}
