<?php

namespace FuncMocker\Test;

use FuncMocker\Mocker;
use FuncMocker\Stream;

require 'fixtures/FooBar.php';

/**
 * @covers \FuncMocker\Mocker
 * @covers \FuncMocker\Func
 */
class MockerTest extends \PHPUnit_Framework_TestCase
{
    public function testCanMockFunction()
    {
        Mocker::mock('time', 'Foo', function () {
            return 12345;
        });
        $this->assertTrue(function_exists('Foo\time'));

        $foobar = new \Foo\Bar();
        $result = $foobar->getTime();
        $this->assertEquals(12345, $result);
    }

    public function testCanDisableAndEnableTheMock()
    {
        $func = Mocker::mock('rand', 'Foo', function ($min, $max) {
            return 10;
        });

        $foobar = new \Foo\Bar();

        $result = $foobar->getRand(1, 9);
        $this->assertEquals(10, $result);

        $func->disable();
        $result = $foobar->getRand(1, 9);
        $this->assertLessThan(10, $result);

        $func->enable();
        $result = $foobar->getRand(1, 9);
        $this->assertEquals(10, $result);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testErrorWhenMockingAnExistingFunction()
    {
        Mocker::mock('rand', 'Foo');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testErrorWhenGettingUnRegisteredFunction()
    {
        Mocker::get('Foo\\Bar\\baz');
    }

    public static function tearDownAfterClass()
    {
        stream_wrapper_unregister(Stream::PROTOCOL);
    }
}
