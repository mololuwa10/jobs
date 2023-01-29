<?php
namespace functions;
use Database\DatabaseTable;
class TruncateTable
{
    private $dbName;
    public function truncateTable() {
        $tableName = ['user', 'category', 'contact', 'job'];

        foreach ($tableName as $table) {
            $tables = new DatabaseTable($table, 'id', $this->dbName);
            $tables->custom('TRUNCATE TABLE ' . $table);
        }
    }
}