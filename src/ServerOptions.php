<?php
namespace Aviogram\CollectD;

class ServerOptions implements ServerOptionsInterface
{
    /**
     * @var string
     */
    protected $address;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @var integer
     */
    protected $bufferLength;

    /**
     * @param string $address       Address to listen on, use 0.0.0.0 for all addresses
     * @param int    $port          The port to listen on (Default: 25826)
     * @param int    $bufferLength  The buffer length for collectd  (1024 for version 4.0 - 4.7), 1452 for Version 5
     */
    function __construct($address = '127.0.0.1', $port = 25826, $bufferLength = 1452)
    {
        $this->address      = $address;
        $this->port         = $port;
        $this->bufferLength = $bufferLength;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return int
     */
    public function getBufferLength()
    {
        return $this->bufferLength;
    }
}
