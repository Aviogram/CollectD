<?php
namespace Aviogram\CollectD;

interface ServerHandlerInterface
{
    /**
     * Handle incoming packets
     *
     * @param Parser\Collection\Packet $packet
     * @param Server                   $server
     *
     * @return void
     */
    public function handle(Parser\Collection\Packet $packet, Server $server);
}
