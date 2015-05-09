<?php
namespace Seedling;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Seedling\Fixture
 */
class FixtureTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getInstance
     * @covers ::__construct
     */
    public function testGetInstance()
    {
        $config = array();
        $driver = $this->getMockBuilder('\Seedling\Drivers\DriverInterface')
            ->getMock();
        $this->assertInstanceOf('\Seedling\Fixture', Fixture::getInstance($config, $driver));
    }

    /**
     * @covers ::setConfig
     * @covers ::getConfig
     * @uses \Seedling\Fixture::getInstance
     */
    public function testConfig()
    {
        $fixture = Fixture::getInstance();
        $this->assertNull($fixture->getConfig());

        $config = array(
            'key' => 'value'
        );
        $fixture->setConfig($config);
        $this->assertEquals($config, $fixture->getConfig());
    }

    /**
     * @covers ::setDriver
     * @covers ::getDriver
     * @uses \Seedling\Fixture::getInstance
     */
    public function testDriver()
    {
        $fixture = Fixture::getInstance();

        $driver = $this->getMockBuilder('\Seedling\Drivers\DriverInterface')
            ->getMock();
        $fixture->setDriver($driver);
        $this->assertEquals($driver, $fixture->getDriver());
    }

    /**
     * @covers ::setFixtures
     * @covers ::getFixtures
     * @uses \Seedling\Fixture::getInstance
     */
    public function testFixtures()
    {
        $fixture = Fixture::getInstance();

        $fixtures = array('table1' => array(), 'table2' => array());

        $this->assertNull($fixture->getFixtures());
        $fixture->setFixtures($fixtures);
        $this->assertEquals($fixtures, $fixture->getFixtures());
    }
}
