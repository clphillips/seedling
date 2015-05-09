<?php
namespace Seedling\Exceptions;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \Seedling\Exceptions\InvalidFixtureDataException
 */
class InvalidFixtureDataExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('\Exception', new InvalidFixtureDataException());
    }
}
