<?php
namespace Seedling\tests\unit\Exceptions;

use PHPUnit_Framework_TestCase;
use Seedling\Exceptions\InvalidFixtureDataException;

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
