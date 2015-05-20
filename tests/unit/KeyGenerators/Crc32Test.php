<?php
namespace Seedling\tests\unit\KeyGenerators;

use PHPUnit_Framework_TestCase;
use Seedling\KeyGenerators\Crc32;

/**
 * @coversDefaultClass \Seedling\KeyGenerators\Crc32
 */
class Crc32Test extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(
            '\Seedling\KeyGenerators\KeyGeneratorInterface',
            new Crc32()
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
            array(new Crc32(), 'foo', null, PHP_INT_MAX > pow(2, 32) ? 2356372769 : 208889122),
            array(new Crc32(10), 'foo', 'bar', 5)
        );
    }
}
