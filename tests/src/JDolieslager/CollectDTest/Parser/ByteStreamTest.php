<?php
namespace Aviogram\CollectDTest\Parser;

use Aviogram\CollectD\Parser\ByteStream;
use PHPUnit_Framework_TestCase;

class ByteStreamTest extends PHPUnit_Framework_TestCase
{
    public function testLength()
    {
        $string = 'i am testing a length';
        $binary = pack('C*', $string);
        $length = strlen($binary);

        $stream = new ByteStream($binary);

        $this->assertEquals($length, $stream->length());
    }

    public function testByte()
    {
        $stream = new ByteStream(hex2bin('0F'));
        $this->assertEquals(15, $stream->byte());
    }

    public function string()
    {
        $string = 'i am testing a length';
        $binary = pack('C*', $string);
        $length = strlen($binary);

        $stream = new ByteStream($binary);

        $this->assertEquals($string, $stream->string($length));
    }

    public function testShort()
    {
        $tests = array(
            array(-2000, pack('s', -2000), false, ByteStream::BYTE_ORDER_MACHINE),
            array(-2000, pack('s', -2000), false, ByteStream::BYTE_ORDER_MACHINE),
            array(2000, pack('S', 2000), true, ByteStream::BYTE_ORDER_MACHINE),
            array(2000, pack('n', 2000), true, ByteStream::BYTE_ORDER_BIG_ENDIAN),
            array(2000, pack('v', 2000), true, ByteStream::BYTE_ORDER_LITTLE_ENDIAN),
        );

        foreach ($tests as $test) {
            $stream = new ByteStream($test[1]);
            $this->assertEquals($test[0], $stream->short($test[2], $test[3]));
        }
    }

    public function testShortInvalidByteType()
    {
        $stream = new ByteStream(pack('s', -2000));
        $this->setExpectedException(
            'Aviogram\CollectD\Parser\Exception\InvalidArgument', 'Invalid byte order type',
            1
        );

        $stream->short(true, '<boehoe>');
    }
}
