<?php
namespace Seedling\tests\unit\KeyGenerators;

use PHPUnit_Framework_TestCase;
use Seedling\KeyGenerators\AutoIncrement;

/**
 * @coversDefaultClass \Seedling\KeyGenerators\AutoIncrement
 */
class AutoIncrementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(
            '\Seedling\KeyGenerators\KeyGeneratorInterface',
            new AutoIncrement()
        );
    }

    /**
     * @covers ::generateKey
     * @dataProvider generateKeyProvider
     */
    public function testGenerateKey($generator, $values, $tables, $results)
    {
        foreach ($values as $i => $value) {
            $this->assertEquals(
                $results[$i],
                $generator->generateKey($values[$i], $tables[$i])
            );
        }
    }

    /**
     * Data Provider
     *
     * @return arary
     */
    public function generateKeyProvider()
    {
        return array(
            array(
                new AutoIncrement(),
                array('label1', 'label2'),
                array(null, null),
                array(1, 2)
            ),
            array(
                new AutoIncrement(4),
                array('label1', 'label2', 'label2', 'label3'),
                array('table1', 'table2', 'table2', 'table2'),
                array(4, 4, 4, 5)
            )
        );
    }
}
