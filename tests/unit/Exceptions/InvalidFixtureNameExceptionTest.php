<?php
namespace Seedling\tests\unit\Exceptions;

use PHPUnit_Framework_TestCase;
use Seedling\Exceptions\InvalidFixtureNameException;

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
