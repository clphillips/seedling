<?php
namespace Seedling\Exceptions;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Seedling\Exceptions\InvalidFixtureNameException
 */
class InvalidFixtureNameExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('\Exception', new InvalidFixtureNameException());
    }
}
