<?php
namespace Aviogram\CollectD\Parser\Entity;

class Time
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
     * @return Time
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
     * @return Time
     */
    public function setHighResolution($highResolution)
    {
        $this->highResolution = $highResolution;

        return $this;
    }

}
