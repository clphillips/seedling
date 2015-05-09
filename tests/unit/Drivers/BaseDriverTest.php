<?php
namespace Seedling\Drivers;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Seedling\Drivers\BaseDriver
 */
class BaseDriverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @uses \Seedling\KeyGenerators\Sha1
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('\Seedling\Drivers\BaseDriver', $this->getDriver(null, null));
    }
    
    /**
     * @covers ::truncate
     * @covers ::__construct
     * @dataProvider truncateProvider
     */
    public function testTruncate($tables, $use_pdo)
    {
        $pdo = null;
        if ($use_pdo) {
            $pdo = $this->getPdo();
            $pdo->expects($this->exactly(count($tables)))
                ->method('query');
        }
        $driver = $this->getDriver($this->getKeyGenerator('Sha1'), $pdo);

        $this->assertNull($driver->truncate($tables));
    }
    
    /**
     * Data provider for testTruncate
     */
    public function truncateProvider()
    {
        return array(
            array(null, true),
            array(array('table1', 'table2'), true),
            array(array('table1', 'table2'), false),
        );
    }

    /**
     * Mock a BaseDriver
     *
     * @param \Seedling\KeyGenerators\KeyGeneratorInterface $generator
     * @param \PDO $pdo
     * @return \Seedling\Drivers\BaseDriver
     */
    protected function getDriver($generator, $pdo)
    {
        return $this->getMockBuilder('\Seedling\Drivers\BaseDriver')
            ->setConstructorArgs(array($generator, $pdo))
            ->setMethods(null)
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
}
