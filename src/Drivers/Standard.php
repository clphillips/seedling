<?php
namespace Seedling\Drivers;

use Seedling\KeyGenerators\KeyGeneratorInterface;
use PDO;

class Standard extends BaseDriver implements DriverInterface
{

    /**
     * Constructor method
     *
     * @param PDO $db
     * @param KeyGeneratorInterface $keyGenerator
     */
    public function __construct(PDO $db, KeyGeneratorInterface $keyGenerator = null)
    {
        parent::__construct($keyGenerator, $db);
    }

    /**
     * Build a fixture record using the passed in values.
     *
     * @param  string $tableName
     * @param  array $records
     * @return array
     */
    public function buildRecords($tableName, array $records)
    {
        $insertedRecords = array();
        $this->tables[$tableName] = $tableName;

        foreach ($records as $recordName => $recordValues) {
            array_walk($recordValues, function (&$value) use ($recordValues) {
                if (is_callable($value)) {
                    $value = call_user_func($value, $recordValues);
                }
            });

            $recordValues = $this->setForeignKeys($recordValues);

            list($table, $label) = $this->parseRecordLabel(
                $recordName[0] === "."
                ? $tableName . $recordName[0]
                : $recordName
            );
            
            if (!is_string($recordName) && !empty($recordName)) {
                $recordValues['id'] = $this->generateKey($label, $table);
            }

            $fields = implode(', ', array_keys($recordValues));
            $values = array_values($recordValues);
            $placeholders = rtrim(str_repeat('?, ', count($recordValues)), ', ');
            $sql = "INSERT INTO $tableName ($fields) VALUES ($placeholders)";

            $sth = $this->db->prepare($sql);
            $sth->execute($values);

            $insertedRecords[$label] = (object) $recordValues;
        }

        return $insertedRecords;
    }

    /**
     * Loop through each of the fixture column/values.
     * If a column ends in '_id' we're going to assume it's
     * a foreign key and we'll hash it's values.
     *
     * @param array $values
     * @return array
     */
    protected function setForeignKeys(array $values)
    {
        foreach ($values as $key => &$value) {
            if ($this->endsWith($key, '_id')) {
                list($table, $label) = $this->parseRecordLabel($value);
                $value = $this->generateKey($label, $table);
            }
        }

        return $values;
    }

    /**
     * Determine if a string ends with a set of specified characters.
     *
     * @param  string $haystack
     * @param  string $needle
     * @return boolean
     */
    protected function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }
}
