<?php
namespace Seedling\tests\unit;

use PHPUnit_Framework_TestCase;
use Seedling\Fixture;

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
        $config = array('key' => 'value');
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

    /**
     * @covers ::__call
     * @uses \Seedling\Fixture::getInstance
     * @uses \Seedling\Fixture::setFixtures
     */
    public function testDynamicMethodCall()
    {
        $fixtures = array(
            'Users' => array(
                'FirstUser' => array(
                    'id' => 1,
                    'username' => 'user1'
                ),
                'SecondUser' => array(
                    'id' => 2,
                    'username' => 'user2'
                )
            ),
            'Roles' => array(
                'Admin' => array(
                    'id' => 1,
                    'name' => 'Admin'
                )
            )
        );
        
        $fixture = Fixture::getInstance();
        $fixture->setFixtures($fixtures);
        
        $this->assertEquals(
            $fixtures['Users']['FirstUser'],
            call_user_func(array($fixture, 'Users'), 'FirstUser')
        );
        $this->assertEquals(
            $fixtures['Roles'],
            call_user_func(array($fixture, 'Roles'))
        );
    }

    /**
     * @covers ::__call
     * @uses \Seedling\Fixture::getInstance
     * @expectedException \Seedling\Exceptions\InvalidFixtureNameException
     */
    public function testDynamicMethodCallException()
    {
        $fixture = Fixture::getInstance();
        $fixture->nonExistentFixture();
    }
    
    /**
     * @covers ::fake
     * @covers ::setFaker
     * @covers ::bootFaker
     */
    public function testFake()
    {
        $value = 15;
        $faker = $this->getMockBuilder('\Seedling\tests\unit\Faker')
            ->disableOriginalConstructor()
            ->getMock();
        $faker->method('numberBetween')
            ->will($this->returnValue($value));
        
        Fixture::setFaker($faker);
        
        $number = Fixture::fake('numberBetween', $value, $value+1);
        $this->assertEquals($value, $number);
        Fixture::setFaker(null);
    }
}
