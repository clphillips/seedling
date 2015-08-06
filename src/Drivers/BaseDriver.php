<?php
namespace Seedling\Drivers;

use Seedling\KeyGenerators\KeyGeneratorInterface;
use Seedling\KeyGenerators\Sha1;
use PDO;

abstract class BaseDriver
{

    /**
     * An array of tables that have had fixture data loaded into them.
     *
     * @var array
     */
    protected $tables = array();

    /**
     * @var KeyGeneratorInterface
     */
    protected $keyGenerator;

    /**
     * @var PDO
     */
    protected $db;

    /**
     * Constructor method
     *
     * @param KeyGeneratorInterface $keyGenerator
     * @param PDO $db
     */
    public function __construct(KeyGeneratorInterface $keyGenerator = null, PDO $db = null)
    {
        if (null === $keyGenerator) {
            $keyGenerator = new Sha1();
        }
        $this->keyGenerator = $keyGenerator;
        $this->db = $db;
    }

    /**
     * Truncate tables
     *
     * @param array $tables The tables to truncate, null for only tables
     * that have been inserted into in this instance.
     * @return void
     */
    public function truncate(array $tables = null)
    {
        if (null === $this->db) {
            return;
        }

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
    public function generateKey($value, $tableName = null)
    {
        return $this->keyGenerator->generateKey($value, $tableName);
    }

    /**
     * Parses a label for its table and label name
     *
     * @param string $label The label, possibly including the table name
     * @return array The table and label
     */
    protected function parseRecordLabel($label)
    {
        $table = null;
        if (false !== strpos($label, '.')) {
            list($table, $label) = explode('.', $label, 2);
        }
        return array($table, $label);
    }
}
