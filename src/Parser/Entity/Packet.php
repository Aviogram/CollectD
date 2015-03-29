<?php
namespace Aviogram\CollectD\Parser\Entity;

use Aviogram\CollectD\Parser\Collection;

class Packet
{
    protected $host;
    protected $time;
    protected $plugin;
    protected $type;
    protected $interval;
    protected $message;
    protected $severity;
    protected $values;

    /**
     * Create underlying objects
     */
    function __construct()
    {
        $this->time       = new Time();
        $this->plugin     = new Plugin();
        $this->type       = new Type();
        $this->interval   = new Interval();
        $this->values     = new Collection\Value();
    }

    /**
     * Clone underlying objects as well
     */
    function __clone()
    {
        $this->time     = clone $this->time;
        $this->plugin   = clone $this->plugin;
        $this->type     = clone $this->type;
        $this->interval = clone $this->interval;
        $this->values   = clone $this->values;
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return Time
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return Plugin
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Interval
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getSeverity()
    {
        return $this->severity;
    }

    /**
     * @return Collection\Value
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param  mixed $host
     *
     * @return Packet
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param  mixed $message
     *
     * @return Packet
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param  mixed $severity
     *
     * @return Packet
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;

        return $this;
    }

    /**
     * @param  Value $value
     *
     * @return Packet
     */
    public function addValue(Value $value)
    {
        $this->getValues()->append($value);

        return $this;
    }

    /**
     * Remove all the values from the collection
     *
     * @return Packet
     */
    public function resetValues()
    {
        $this->values = new Collection\Value();

        return $this;
    }
}
