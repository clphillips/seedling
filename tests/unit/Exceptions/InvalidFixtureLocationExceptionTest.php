<?php
namespace Seedling\Exceptions;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Seedling\Exceptions\InvalidFixtureLocationException
 */
class InvalidFixtureLocationExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('\Exception', new InvalidFixtureLocationException());
    }
}
