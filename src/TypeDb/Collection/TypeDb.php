<?php
namespace Aviogram\CollectD\TypeDb\Collection;

use Aviogram\CollectD\TypeDb\Entity;
use Aviogram\Common\AbstractCollection;

/**
 * @method Entity\Plugin current()
 * @method Entity\Plugin offsetGet($offset)
 */
class TypeDb extends AbstractCollection
{
    /**
     * Determines of the value is a valid collection value
     *
     * @param  mixed $value
     * @return boolean
     */
    protected function isValidValue($value)
    {
        return ($value instanceof Entity\Plugin);
    }
}
