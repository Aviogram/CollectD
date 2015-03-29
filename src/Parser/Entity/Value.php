<?php
namespace Aviogram\CollectD\Parser\Entity;

class Value
{
    /**
     * @var integer
     */
    protected $type;

    /**
     * @var integer|double
     */
    protected $value;

    /**
     * Create new value object
     *
     * @param integer        $type
     * @param integer|double $value
     */
    function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return integer | double
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return boolean
     */
    public function isCounter()
    {
        return $this->type === 0;
    }

    /**
     * @return boolean
     */
    public function isGauge()
    {
        return $this->type === 1;
    }

    /**
     * @return boolean
     */
    public function isDerive()
    {
        return $this->type === 2;
    }

    /**
     * @return boolean
     */
    public function isAbsolute()
    {
        return $this->type === 3;
    }
}
