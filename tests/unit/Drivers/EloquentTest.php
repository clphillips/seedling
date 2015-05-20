<?php
namespace Seedling\tests\unit\Drivers;

use PHPUnit_Framework_TestCase;
use Seedling\Drivers\Eloquent;

/**
 * @coversDefaultClass \Seedling\Drivers\Eloquent
 */
class EloquentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @uses \Seedling\Drivers\BaseDriver::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(
            '\Seedling\Drivers\DriverInterface',
            new Eloquent($this->getPdo(), $this->getStr(), $this->getKeyGenerator('Sha1'))
        );
    }
    
    /**
     * @covers ::buildRecords
     * @covers ::__construct
     * @covers ::insertRelatedRecords
     * @covers ::camelCase
     * @covers \Seedling\Drivers\BaseDriver::generateKey
     * @uses \Seedling\Drivers\BaseDriver::__construct
     */
    public function testBuildRecords()
    {
        $tableName = 'table1';
        $model = 'Table1';
        $records = array(
            'Name1' => array(
                'foreign_id' => 'ForeignName1',
                'field1' => "value1",
                'field2' => "value2",
                'field3' => function ($items) {
                    return $items['field1'] . $items['field2'];
                }
            ),
            'Name2' => array(
                'foreign_id' => 'ForeignName2',
                'field1' => "value1",
                'field2' => "value2"
            )
        );
        $keyGenerator = $this->getKeyGenerator('Sha1');

        $pdoStatement = $this->getMockBuilder('\PDOStatement')->getMock();
        $pdoStatement->expects($this->any())
            ->method('execute');

        $pdo = $this->getPdo();
        $pdo->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($pdoStatement));

        $str = $this->getStr();
        $model = $this->getModel();

        $driver = $this->getDriver($pdo, $str, $keyGenerator);
        $driver->expects($this->any())
            ->method('generateModelName')
            ->will($this->returnValue($model));
        $driver->expects($this->any())
            ->method('createModel')
            ->will($this->returnValue($model));

        $this->assertNotEmpty($driver->buildRecords($tableName, $records));
    }

    /**
     * Get instance of the driver
     *
     * @return \Seedling\Drivers\Eloquent
     */
    protected function getDriver($pdo, $str, $keyGenerator)
    {
        return $this->getMockBuilder('\Seedling\Drivers\Eloquent')
            ->setConstructorArgs(array($pdo, $str, $keyGenerator))
            ->setMethods(array('generateModelName', 'createModel'))
            ->getMock();
    }

    /**
     * Mock a concrete KeyGeneratorInterface
     *
     * @param string $generator The key generator to mock
     * @return \Seedling\KeyGenerators\KeyGeneratorInterface
     */
    protected function getKeyGenerator($generator)
    {
        $generatorMock = $this->getMockBuilder('\Seedling\KeyGenerators\\' . $generator)
            ->disableOriginalConstructor()
            ->getMock();
        return $generatorMock;
    }

    /**
     * Mock PDO
     *
     * @return \PDO
     */
    protected function getPdo()
    {
        return $this->getMockBuilder('\PDO')
            ->setConstructorArgs(array('sqlite::memory:'))
            ->getMock();
    }

    /**
     * Mock \Illuminate\Support\Str
     *
     * @return \Illuminate\Support\Str
     */
    protected function getStr()
    {
        return $this->getMockBuilder('\Illuminate\Support\Str')
            ->getMock();
    }

    /**
     * Mock \Illuminate\Database\Eloquent\Model
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function getModel()
    {
        return $this->getMockBuilder('\Illuminate\Database\Eloquent\Model')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
