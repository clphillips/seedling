<?php
namespace Seedling\KeyGenerators;

/**
 * Auto Incrementing Key Generator
 */
class AutoIncrement implements KeyGeneratorInterface
{

    /**
     * @var int The starting ID
     */
    private $start;

    /**
     * @var array An array of tables and their next auto-increment ID
     */
    private $tables = array();

    /**
     * @var array An array of assigned IDs
     */
    private $ids = array();
    
    /**
     * Initialize the first ID
     *
     * @param int $start
     */
    public function __construct($start = 1)
    {
        $this->start = $start;
    }

    /**
     * {@inheritdoc}
     */
    public function generateKey($value, $tableName = null)
    {
        $id = $tableName . "." . $value;

        if (!array_key_exists($tableName, $this->tables)) {
            $this->tables[$tableName] = $this->start;
        }
        if (!array_key_exists($id, $this->ids)) {
            $this->ids[$id] = $this->tables[$tableName]++;
        }
        
        return $this->ids[$id];
    }
}
