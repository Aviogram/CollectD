<?php
namespace Aviogram\CollectD\Parser;

class ByteStream
{
    const BYTE_ORDER_MACHINE       = 1,
          BYTE_ORDER_BIG_ENDIAN    = 2,
          BYTE_ORDER_LITTLE_ENDIAN = 3;

    /**
     * The binary data
     *
     * @var array
     */
    protected $data;

    public function __construct($binarydata)
    {
        $this->data = unpack('C*', $binarydata);
    }

    /**
     * @return integer
     */
    public function length()
    {
        return count($this->data);
    }

    /**
     * Read an single byte
     *
     * @param boolean $unsigned
     *
     * @return integer
     */
    public function byte($unsigned = true)
    {
        $byte   = array_shift($this->data);
        $format = $unsigned ? 'C' : 'c';

        $pack   = pack('C', $byte);
        $unpack = unpack($format, $pack);

        return $unpack[1];
    }

    /**
     * Retrieve an string representation of the binary data
     *
     * @param integer $length   How many bytes to read
     *
     * @return string
     */
    public function string($length)
    {
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $int     = array_shift($this->data);

            if ($int > 0) {
                $string .= chr($int);
            }
        }

        return $string;
    }

    /**
     * Retrieve an 16 byte number
     *
     * @param boolean $unsigned If the number is unsigned or not
     * @param integer $byteOrder What the byte order is
     *
     * @return integer
     * @throws Exception\InvalidArgument
     */
    public function short($unsigned = true, $byteOrder = self::BYTE_ORDER_BIG_ENDIAN)
    {
        switch ($byteOrder) {
            case static::BYTE_ORDER_MACHINE:
                $format = ($unsigned) ? 'S' : 's';
                break;
            case static::BYTE_ORDER_LITTLE_ENDIAN:
                $format = 'v';
                break;
            case static::BYTE_ORDER_BIG_ENDIAN:
                $format = 'n';
                break;
            default:
                throw new Exception\InvalidArgument('Invalid byte order type', 1);
        }

        $one = array_shift($this->data);
        $two = array_shift($this->data);

        $pack   = pack('CC', $one, $two);
        $unpack = unpack($format, $pack);

        return $unpack[1];
    }

    /**
     * Retrieve an 64 byte number
     *
     * @param boolean $unsigned If the number is unsigned or not
     * @param integer $byteOrder What the byte order is
     *
     * @return integer | double on PHP_INT_SIZE = 4, integer on PHP_INT_SIZE = 8
     * @throws Exception\InvalidArgument
     */
    public function longLong($unsigned = true, $byteOrder = self::BYTE_ORDER_BIG_ENDIAN)
    {
        // Rebuild binary string
        $one   = array_shift($this->data);
        $two   = array_shift($this->data);
        $three = array_shift($this->data);
        $four  = array_shift($this->data);
        $five  = array_shift($this->data);
        $six   = array_shift($this->data);
        $seven = array_shift($this->data);
        $eight = array_shift($this->data);
        $pack  = pack('CCCCCCCC', $one, $two, $three, $four, $five, $six, $seven, $eight);

        // Use PHP internal 64bit system when available
        /* if (PHP_INT_SIZE > 4 && PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION >= 6) {
            switch ($byteOrder) {
                case static::BYTE_ORDER_MACHINE:
                    $format = ($unsigned) ? 'Q' : 'q';
                    break;
                case static::BYTE_ORDER_LITTLE_ENDIAN:
                    $format = 'P';
                    break;
                case static::BYTE_ORDER_BIG_ENDIAN:
                    $format = 'J';
                    break;
                default:
                    throw new Exception\InvalidArgument('Invalid byte order type', 1);
            }

            $unpack = unpack($format, $pack);

            return $unpack[1];
        }
        */

        $hex    = bin2hex($pack);
        $hexDec = '0123456789abcdef';
        $length = strlen($hex);
        $number = 0;

        // Convert base16 to base10
        for ($i = 0, $j = $length - 1; $i < $length; $i++, $j--) {
            $number += strpos($hexDec, $hex[$i]) * pow(16, $j);
        }

        // Convert HEX 16 base to an INT 10 base (is a string)
        if (PHP_INT_SIZE > 4 && PHP_INT_MAX >= $number) {
            return (int) $number;
        }

        // Return integer based on comp. for x32 on x64 system
        return (float) $number;
    }

    /**
     * Return double integer
     *
     * @param integer $bytes    The amount of bytes to read
     *
     * @return double
     */
    public function double($bytes = 1)
    {
        $arguments = array(str_repeat('C', $bytes));
        for ($i = 0; $i < $bytes; $i++) {
            $arguments[] = array_shift($this->data);
        }

        $pack = call_user_func_array('pack', $arguments);
        $unpack = unpack('d', $pack);

        return $unpack[1];
    }
}
