<?php
namespace Aviogram\CollectD\TypeDb\Entity;

use Aviogram\Common\AbstractEntity;

class PluginValue extends AbstractEntity
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var float
     */
    protected $min;

    /**
     * @var float|null
     */
    protected $max;

    /**
     * @param string $name
     * @param string $type
     * @param float  $min
     * @param float  $max
     */
    public function __construct($name, $type, $min, $max)
    {
        $this->name = $name;
        $this->type = $type;
        $this->min  = $min;
        $this->max  = $max;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return float|null
     */
    public function getMax()
    {
        return $this->max;
    }


    /**
     * @return boolean
     */
    public function isCounter()
    {
        return $this->type === 'COUNTER';
    }

    /**
     * @return boolean
     */
    public function isGauge()
    {
        return $this->type === 'GAUGE';
    }

    /**
     * @return boolean
     */
    public function isDerive()
    {
        return $this->type === 'DERIVE';
    }

    /**
     * @return boolean
     */
    public function isAbsolute()
    {
        return $this->type === 'ABSOLUTE';
    }
}
