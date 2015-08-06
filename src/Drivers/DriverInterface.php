<?php namespace Seedling\Drivers;

interface DriverInterface
{

    /**
     * Build a fixture record using the passed in values.
     *
     * @param  string $tableName
     * @param  array $records
     * @return array
     */
    public function buildRecords($tableName, array $records);

    /**
     * Truncate a table.
     *
     * @return void
     */
    public function truncate(array $tables = null);

    /**
     * Generate a key using the provided key generator
     *
     * @param string $value
     * @param string $tableName
     */
    public function generateKey($value, $tableName = null);
}
