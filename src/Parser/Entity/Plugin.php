<?php
namespace Aviogram\CollectD\Parser\Entity;

class Plugin
{
    protected $name;
    protected $instanceName;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  mixed $name
     *
     * @return Plugin
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInstanceName()
    {
        return $this->instanceName;
    }

    /**
     * @param  mixed $instanceName
     *
     * @return Plugin
     */
    public function setInstanceName($instanceName)
    {
        $this->instanceName = $instanceName;

        return $this;
    }

}
