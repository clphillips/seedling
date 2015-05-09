<?php namespace Seedling\Drivers;

use Seedling\KeyGenerators\KeyGeneratorInterface;
use Seedling\KeyGenerators\Sha1;

abstract class BaseDriver
{

    /**
     * An array of tables that have had fixture data loaded into them.
     *
     * @var array
     */
    protected $tables = array();
    
    protected $keyGenerator;
    
    /**
     * Constructor method
     *
     * @param KeyGeneratorInterface $keyGenerator
     */
    public function __construct(KeyGeneratorInterface $keyGenerator = null)
    {
        if (null === $keyGenerator) {
            $keyGenerator = new Sha1();
        }
        $this->keyGenerator = $keyGenerator;
    }

    /**
     * Truncate a table.
     *
     * @param array $tables The tables to truncate, null for only tables
     * that have been inserted into in this instance.
     * @return void
     */
    public function truncate(array $tables = null)
    {
        if (null === $tables) {
            $tables = $this->tables;
        }
        
        foreach ($tables as $table) {
            $this->db->query("DELETE FROM $table");
        }

        $this->tables = array_diff($this->tables, $tables);
    }

    /**
     * Generate a key using the provided key generator
     *
     * @param string $value
     * @param string $tableName
     */
    protected function generateKey($value, $tableName = null)
    {
        return $this->keyGenerator->generateKey($value, $tableName);
    }

    /**
     * Set tables that the driver to process
     */
    protected function setTables(array $tables)
    {
        
    }
}
