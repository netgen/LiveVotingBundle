<?php

namespace Netgen\LiveVotingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 */
class Event
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $stateName;

    /**
     * @var string
     */
    private $stateValue;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set stateName
     *
     * @param string $stateName
     * @return Event
     */
    public function setStateName($stateName)
    {
        $this->stateName = $stateName;

        return $this;
    }

    /**
     * Get stateName
     *
     * @return string 
     */
    public function getStateName()
    {
        return $this->stateName;
    }

    /**
     * Set stateValue
     *
     * @param string $stateValue
     * @return Event
     */
    public function setStateValue($stateValue)
    {
        $this->stateValue = $stateValue;

        return $this;
    }

    /**
     * Get stateValue
     *
     * @return string 
     */
    public function getStateValue()
    {
        return $this->stateValue;
    }
}
