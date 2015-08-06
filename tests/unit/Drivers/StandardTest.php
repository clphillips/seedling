<?php
namespace Seedling\tests\unit\Drivers;

use PHPUnit_Framework_TestCase;
use Seedling\Drivers\Standard;

/**
 * @coversDefaultClass \Seedling\Drivers\Standard
 */
class StandardTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @uses \Seedling\Drivers\BaseDriver::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(
            '\Seedling\Drivers\DriverInterface',
            new Standard($this->getPdo(), $this->getKeyGenerator('Sha1'))
        );
    }

    /**
     * @covers ::buildRecords
     * @covers ::__construct
     * @covers ::setForeignKeys
     * @covers ::endsWith
     * @covers ::quoteIdentifier
     * @covers \Seedling\Drivers\BaseDriver::generateKey
     * @covers \Seedling\Drivers\BaseDriver::parseRecordLabel
     * @uses \Seedling\Drivers\BaseDriver::__construct
     */
    public function testBuildRecords()
    {
        $tableName = 'table1';
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
            ),
            '.Name2' => array(
                'foreign_id' => 'foreigns.ForeignName2',
                'field1' => "value1",
                'field2' => "value2"
            ),
        );
        $keyGenerator = $this->getKeyGenerator('Sha1');

        $pdoStatement = $this->getMockBuilder('\PDOStatement')->getMock();
        $pdoStatement->expects($this->any())
            ->method('execute');

        $pdo = $this->getPdo();
        $pdo->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($pdoStatement));

        $driver = new Standard($pdo, $keyGenerator);

        $this->assertNotEmpty($driver->buildRecords($tableName, $records));
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
}
