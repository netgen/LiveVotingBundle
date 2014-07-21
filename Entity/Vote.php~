<?php

namespace Netgen\LiveVotingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vote
 */
class Vote
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $rate;

    /**
     * @var \Netgen\LiveVotingBundle\Entity\User
     */
    private $user;

    /**
     * @var \Netgen\LiveVotingBundle\Entity\Presentation
     */
    private $presentation;


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
     * Set rate
     *
     * @param integer $rate
     * @return Vote
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return integer 
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set user
     *
     * @param \Netgen\LiveVotingBundle\Entity\User $user
     * @return Vote
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

    /**
     * Set presentation
     *
     * @param \Netgen\LiveVotingBundle\Entity\Presentation $presentation
     * @return Vote
     */
    public function setPresentation(\Netgen\LiveVotingBundle\Entity\Presentation $presentation = null)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     *
     * @return \Netgen\LiveVotingBundle\Entity\Presentation 
     */
    public function getPresentation()
    {
        return $this->presentation;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $event;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->event = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add event
     *
     * @param \Netgen\LiveVotingBundle\Entity\Event $event
     * @return Vote
     */
    public function addEvent(\Netgen\LiveVotingBundle\Entity\Event $event)
    {
        $this->event[] = $event;

        return $this;
    }

    /**
     * Remove event
     *
     * @param \Netgen\LiveVotingBundle\Entity\Event $event
     */
    public function removeEvent(\Netgen\LiveVotingBundle\Entity\Event $event)
    {
        $this->event->removeElement($event);
    }

    /**
     * Get event
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvent()
    {
        return $this->event;
    }
}
