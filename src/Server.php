<?php
namespace Aviogram\CollectD;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Server
{
    /**
     * @var ServerOptionsInterface
     */
    protected $options;

    /**
     * @var ServerHandlerInterface[]
     */
    protected $handlers = array();

    /**
     * @var Parser\ServerParser
     */
    protected $parser;

    /**
     * @var boolean
     */
    private $useFork;

    /**
     * Create a server listener for CollectD data
     *
     * @param ServerOptionsInterface $options
     * @param LoggerInterface        $logger
     */
    public function __construct(ServerOptionsInterface $options, LoggerInterface $logger = null)
    {
        $this->options = $options;
        $this->parser  = new Parser\ServerParser();

        if ($logger === null) {
            $logger = new NullLogger();
        }

        $this->logger  = $logger;
        $this->useFork = extension_loaded('pcntl');
    }

    /**
     * @return ServerOptionsInterface
     */
    protected function getOptions()
    {
        return $this->options;
    }

    public function listen()
    {
        $address      = $this->getOptions()->getAddress();
        $port         = $this->getOptions()->getPort();
        $bufferLength = $this->getOptions()->getBufferLength();

        $this->getLogger()->debug('Creating socket');
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

        $this->getLogger()->debug("Bind socket on {$address}:{$port}");
        socket_bind($socket, $this->getOptions()->getAddress(), $this->getOptions()->getPort());

        while (true) {
            $from = $fromPort = $buffer = null;

            socket_recvfrom($socket, $buffer, $bufferLength, 0, $from, $fromPort);

            $this->getLogger()->debug("Got incoming data from {$from}:{$fromPort}");

            if ($this->useFork === false) {
                $this->getLogger()->debug('Could not fork process. Extension PCNTL is not loaded.');
                $this->handleRequest($buffer, $from, $fromPort);

            } else {
                $pid = pcntl_fork();

                if ($pid === -1) {
                    $this->getLogger()->debug('Could not fork process. Fork failed.');
                    $this->handleRequest($buffer, $from, $fromPort);
                } else if ($pid) {
                    // Main process continues
                    continue;
                } else {
                    $this->getLogger()->debug('Process forked.');
                    $this->handleRequest($buffer, $from, $fromPort);

                    // Exit child process
                    exit;
                }
            }
        }

        $this->getLogger()->debug("Close socket on {$address}:{$port}");
        socket_close($socket);
    }

    /**
     * Register a new handler on the server
     *
     * @param ServerHandlerInterface $handler
     *
     * @return boolean TRUE when registration was successful | FALSE when the handler was already registered
     */
    public function registerHandler(ServerHandlerInterface $handler)
    {
        $id = spl_object_hash($handler);

        if (array_key_exists($id, $this->handlers) === false) {
            $this->handlers[$id] = $handler;

            return true;
        }

        return false;
    }

    /**
     * Remove an handler from the server handler list
     *
     * @param ServerHandlerInterface $handler
     *
     * @return boolean TRUE when successful unregister | FALSE when the handler was not found
     */
    public function unRegisterHandler(ServerHandlerInterface $handler)
    {
        $id = spl_object_hash($handler);

        if (array_key_exists($id, $this->handlers) === false) {
            return false;
        }

        unset($this->handlers[$id]);

        return true;
    }

    /**
     * Handle incoming request
     *
     * @param binary  $buffer
     * @param string  $from
     * @param integer $fromPort
     *
     * @return void
     */
    protected function handleRequest($buffer, $from, $fromPort)
    {
        $context = array('from' => $from, 'port' => $fromPort);

        try {
            $packet = $this->getParser()->parse($buffer);
        } catch (Parser\Exception\UnSupported $e) {
            $context['exception'] = $e;
            $this->getLogger()->error('Parsing incoming data failed', $context);

            return;
        }

        $this->getLogger()->debug('Parsed ' . $packet->count() . ' packets from incoming data', $context);
        $this->getLogger()->debug('Inform ' . count($this->handlers) . ' handlers for new data', $context);

        // Inform all the handlers
        foreach ($this->handlers as $handler) {
            // Do not fork when nog possible
            if ($this->useFork === false) {
                $handler->handle($packet, $this);
            } else {
                // Fork process for faster processing
                $pid = pcntl_fork();
                if ($pid === -1) {
                    $this->getLogger()->debug('Could not fork process for calling handler.', $context);
                    $handler->handle($packet, $this);
                } else if ($pid) {
                    // Process has been forked (Main thread)
                    continue;
                } else {
                    // Fork code.
                    $handler->handle($packet, $this);
                    // Close child process
                    exit;
                }
            }
        }

        // Wait till all child processes has been completed
        if ($this->useFork === true) {
            while(pcntl_waitpid(0, $status) !== -1) {
                // Wait
                usleep(100);
            }
        }
    }

    /**
     * @return Parser\ServerParser
     */
    protected function getParser()
    {
        return $this->parser;
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->logger;
    }
}
