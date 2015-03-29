<?php
namespace Aviogram\CollectD;

interface ServerOptionsInterface
{
    /**
     * Get the address to listen on
     *
     * @return string (Example 0.0.0.0, 127.0.0.1)
     */
    public function getAddress();

    /**
     * Get the port to listen on
     *
     * @return integer
     */
    public function getPort();

    /**
     * Return the buffer length for the incoming data
     *
     * @return integer
     */
    public function getBufferLength();
}
