<?php

namespace FuncMocker\Test;

use FuncMocker\Stream;

/**
 * @covers \FuncMocker\Stream
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{
    public function testStreamWrapperBasics()
    {
        stream_wrapper_register(Stream::PROTOCOL, Stream::class);

        $filename = Stream::PROTOCOL . '://function(){}';
        filesize($filename); // Exercises stat, but don't care about result.

        $data = '';
        $stream = fopen($filename, 'r');
        while (!feof($stream)) {
            $data .= fread($stream, 4);
        }

        $this->assertEquals('<?php function(){}', $data);
    }

    public static function tearDownAfterClass()
    {
        stream_wrapper_unregister(Stream::PROTOCOL);
    }
}
