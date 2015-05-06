<?php
namespace Aviogram\CollectD\TypeDb\Collection;

use Aviogram\CollectD\TypeDb\Entity;
use Aviogram\Common\AbstractCollection;

/**
 * @method Entity\PluginValue current()
 * @method Entity\PluginValue offsetGet($offset)
 */
class PluginValue extends AbstractCollection
{
    /**
     * Determines of the value is a valid collection value
     *
     * @param  mixed $value
     * @return boolean
     */
    protected function isValidValue($value)
    {
        return ($value instanceof Entity\PluginValue);
    }
}
