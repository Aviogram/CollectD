<?php
namespace Aviogram\CollectD\TypeDb\Entity;

use Aviogram\CollectD\TypeDb\Collection;
use Aviogram\Common\AbstractEntity;

class Plugin extends AbstractEntity
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Collection\PluginValue
     */
    protected $values;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name   = $name;
        $this->values = new Collection\PluginValue();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Collection\PluginValue
     */
    public function getValues()
    {
        return $this->values;
    }
}
