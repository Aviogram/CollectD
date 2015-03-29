<?php
namespace Aviogram\CollectD\Parser\Entity;

class Interval
{
    protected $normal;
    protected $highResolution;

    /**
     * @return mixed
     */
    public function getNormal()
    {
        return $this->normal;
    }

    /**
     * @param  mixed $normal
     *
     * @return Interval
     */
    public function setNormal($normal)
    {
        $this->normal = $normal;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHighResolution()
    {
        return $this->highResolution;
    }

    /**
     * @param  mixed $highResolution
     *
     * @return Interval
     */
    public function setHighResolution($highResolution)
    {
        $this->highResolution = $highResolution;

        return $this;
    }
}
