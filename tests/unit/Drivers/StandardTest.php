<?php
namespace Seedling\Drivers;

use PHPUnit_Framework_TestCase;

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
