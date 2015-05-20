<?php
namespace Seedling\tests\unit\Exceptions;

use PHPUnit_Framework_TestCase;
use Seedling\Exceptions\InvalidFixtureLocationException;

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
