<?php

namespace Netgen\LiveVotingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Registration
 */
class Registration
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $devLevel;

    /**
     * @var \DateTime
     */
    private $arrivalTime;

    /**
     * @var \DateTime
     */
    private $departureTime;

    private $event;
    private $user;


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
     * Set devLevel
     *
     * @param string $devLevel
     * @return Registration
     */
    public function setDevLevel($devLevel)
    {
        $this->devLevel = $devLevel;

        return $this;
    }

    /**
     * Get devLevel
     *
     * @return string 
     */
    public function getDevLevel()
    {
        return $this->devLevel;
    }

    /**
     * Set arrivalTime
     *
     * @param \DateTime $arrivalTime
     * @return Registration
     */
    public function setArrivalTime($arrivalTime)
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    /**
     * Get arrivalTime
     *
     * @return \DateTime 
     */
    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }

    /**
     * Set departureTime
     *
     * @param \DateTime $departureTime
     * @return Registration
     */
    public function setDepartureTime($departureTime)
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    /**
     * Get departureTime
     *
     * @return \DateTime 
     */
    public function getDepartureTime()
    {
        return $this->departureTime;
    }

    /**
     * Set event
     *
     * @param \Netgen\LiveVotingBundle\Entity\Event $event
     * @return Registration
     */
    public function setEvent(\Netgen\LiveVotingBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \Netgen\LiveVotingBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set user
     *
     * @param \Netgen\LiveVotingBundle\Entity\User $user
     * @return Registration
     */
    public function setUser(\Netgen\LiveVotingBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Netgen\LiveVotingBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
