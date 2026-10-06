<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        $this->addIndex('transaction_details', 'transaction_details_dealer_id_date_index', ['dealer_id', 'date']);
        $this->addIndex('dealers', 'dealers_status_index', ['status']);
        $this->addIndex('clients', 'clients_status_name_index', ['status', 'name']);
        $this->addIndex('dealer_stock_requests', 'dealer_stock_requests_status_index', ['status']);
    }

    public function down()
    {
        foreach ([
            ['transaction_details', 'transaction_details_dealer_id_date_index'],
            ['dealers', 'dealers_status_index'],
            ['clients', 'clients_status_name_index'],
            ['dealer_stock_requests', 'dealer_stock_requests_status_index'],
        ] as $index) {
            DB::statement('ALTER TABLE `' . $index[0] . '` DROP INDEX `' . $index[1] . '`');
        }
    }

    private function addIndex($table, $name, array $columns)
    {
        $tableColumns = collect(DB::select('SHOW COLUMNS FROM `' . $table . '`'))->pluck('Field');
        if (collect($columns)->diff($tableColumns)->isNotEmpty()) {
            return;
        }
        $exists = collect(DB::select('SHOW INDEX FROM `' . $table . '`'))->contains(function ($index) use ($name) {
            return $index->Key_name === $name;
        });
        if (! $exists) {
            DB::statement('ALTER TABLE `' . $table . '` ADD INDEX `' . $name . '` (`' . implode('`, `', $columns) . '`)');
        }
    }
}
