<?php
namespace Seedling\tests\unit\KeyGenerators;

use PHPUnit_Framework_TestCase;
use Seedling\KeyGenerators\Sha1;

/**
 * @coversDefaultClass \Seedling\KeyGenerators\Sha1
 */
class Sha1Test extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(
            '\Seedling\KeyGenerators\KeyGeneratorInterface',
            new Sha1()
        );
    }

    /**
     * @covers ::generateKey
     * @dataProvider generateKeyProvider
     */
    public function testGenerateKey($generator, $value, $table, $result)
    {
        $this->assertEquals($result, $generator->generateKey($value, $table));
    }

    /**
     * Data Provider
     *
     * @return arary
     */
    public function generateKeyProvider()
    {
        return array(
            array(new Sha1(), 'foo', null, 68123873),
            array(new Sha1(4), 'foo', 'bar', 5498)
        );
    }
}
