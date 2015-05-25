<?php
namespace Seedling\Drivers;

use Seedling\KeyGenerators\KeyGeneratorInterface;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use PDO;

class Eloquent extends BaseDriver implements DriverInterface
{

    /**
     * An instance of Laravel's Str class.
     *
     * @var Str
     */
    protected $str;

    /**
     * Constructor method
     *
     * @param  DatabaseManager $db
     * @param  Str $str
     * @param KeyGeneratorInterface $keyGenerator
     */
    public function __construct(PDO $db, Str $str, KeyGeneratorInterface $keyGenerator = null)
    {
        parent::__construct($keyGenerator, $db);
        $this->str = $str;
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
            $model = $this->generateModelName($tableName);
            $record = $this->createModel($this->generateModelName($tableName));

            foreach ($recordValues as $columnName => $columnValue) {
                $camelKey = $this->camelCase($columnName);

                if (is_callable($columnValue)) {
                    $columnValue = call_user_func($columnValue, $recordValues);
                }

                // If a column name exists as a method on the model, we will just assume
                // it is a relationship and we'll generate the primary key for it and store
                // it as a foreign key on the model.
                if (method_exists($record, $camelKey)) {
                    $this->insertRelatedRecords($recordName, $record, $camelKey, $columnValue);

                    continue;
                }

                $record->$columnName = $columnValue;
            }

            $primaryKeyName = $record->getKeyName();
            list($table, $label) = $this->parseRecordLabel(
                $recordName[0] === "."
                ? $tableName . $recordName[0]
                : $recordName
            );
            $record->$primaryKeyName = $this->generateKey($label, $table);
            $record->save();
            $insertedRecords[$recordName] = $record;
        }

        return $insertedRecords;
    }

    /**
     * Insert related records for a fixture.
     *
     * @param  string $recordName
     * @param  Model $record
     * @param  string $camelKey
     * @param  string $columnValue
     * @return void
     */
    protected function insertRelatedRecords($recordName, Model $record, $camelKey, $columnValue)
    {
        $relation = $record->$camelKey();

        if ($relation instanceof BelongsTo) {
            $this->insertBelongsTo($record, $relation, $columnValue);

            return;
        }

        if ($relation instanceof BelongsToMany) {
            $this->insertBelongsToMany($recordName, $relation, $columnValue);

            return;
        }
    }

    /**
     * Insert a belongsTo foreign key relationship.
     *
     * @param  Model $record
     * @param  Relation $relation
     * @param  int $columnValue
     * @return void
     */
    protected function insertBelongsTo(Model $record, Relation $relation, $columnValue)
    {
        $foreignKeyName = $relation->getForeignKey();
        list($table, $label) = $this->parseRecordLabel($columnValue);
        $foreignKeyValue = $this->generateKey($label, $table);
        $record->$foreignKeyName = $foreignKeyValue;
    }

    /**
     * Insert a belongsToMany foreign key relationship.
     *
     * @param  string recordName
     * @param  Relation $relation
     * @param  int $columnValue
     * @return void
     */
    protected function insertBelongsToMany($recordName, Relation $relation, $columnValue)
    {
        $joinTable = $relation->getTable();
        $this->tables[] = $joinTable;
        $relatedRecords = explode(',', str_replace(', ', ',', $columnValue));

        foreach ($relatedRecords as $relatedRecord) {
            list($fields, $values) = $this->buildBelongsToManyRecord($recordName, $relation, $relatedRecord);
            $placeholders = rtrim(str_repeat('?, ', count($values)), ', ');
            $sql = "INSERT INTO $joinTable ($fields) VALUES ($placeholders)";
            $sth = $this->db->prepare($sql);
            $sth->execute($values);
        }
    }

    /**
     * Parse the fixture data for belongsToManyRecord.
     * The current syntax allows for pivot data to be provided
     * via a pipe delimiter with colon separated key values.
     * <code>
     *    'Travis' => [
     *        'first_name'   => 'Travis',
     *        'last_name'    => 'Bennett',
     *        'roles'         => 'endUser|foo:bar, root'
     *    ]
     * </code>
     *
     * @param  string $recordName The name of the relation the fixture is defined on (e.g Travis).
     * @param  Relation $relation The relationship oject (should be of type belongsToMany).
     * @param  string $relatedRecord The related record data (e.g endUser|foo:bar or root).
     * @return array
     */
    protected function buildBelongsToManyRecord($recordName, Relation $relation, $relatedRecord)
    {
        $pivotColumns = explode('|', $relatedRecord);
        $relatedRecordName = array_shift($pivotColumns);

        $foreignKeyPieces = explode('.', $relation->getForeignKey());
        $foreignKeyName = $foreignKeyPieces[1];
        list($table, $label) = $this->parseRecordLabel($recordName);
        $foreignKeyValue = $this->generateKey($label, $table);

        $otherKeyPieces = explode('.', $relation->getOtherKey());
        $otherKeyName = $otherKeyPieces[1];
        list($table, $label) = $this->parseRecordLabel($relatedRecordName);
        $otherKeyValue = $this->generateKey($label, $table);
        
        $fields = "$foreignKeyName, $otherKeyName";
        $values = array($foreignKeyValue, $otherKeyValue);
        
        foreach ($pivotColumns as $pivotColumn) {
            list($columnName, $columnValue) = explode(':', $pivotColumn);
            $fields .= ", $columnName";
            $values[] = $columnValue;
        }

        return array($fields, $values);
    }

    /**
     * Generate the name of table's corresponding model.
     *
     * @param  string $tableName
     * @return string
     */
    protected function generateModelName($tableName)
    {
        return $this->str->singular(str_replace(' ', '', ucwords(str_replace('_', ' ', $tableName))));
    }
    
    /**
     * Convert to camelCase
     *
     * @param string $str The value to convert
     * @return string
     */
    protected function camelCase($str)
    {
        return lcfirst(
            str_replace(
                ' ',
                '',
                ucwords(str_replace(array('-', '_'), ' ', $str))
            )
        );
    }
    
    /**
     * Create an instance of the given model
     *
     * @param string $modelName
     * @return object
     */
    protected function createModel($modelName)
    {
        return new $modelName;
    }
}
