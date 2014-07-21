<?php

namespace Netgen\LiveVotingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Presentation
 */
class Presentation
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $presenterName;

    /**
     * @var string
     */
    private $presenterSurname;

    /**
     * @var string
     */
    private $presentationName;

    /**
     * @var boolean
     */
    private $votingEnabled;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $vote;

    /**
     * @var \Netgen\LiveVotingBundle\Entity\Event
     */
    private $event;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->vote = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set presenterName
     *
     * @param string $presenterName
     * @return Presentation
     */
    public function setPresenterName($presenterName)
    {
        $this->presenterName = $presenterName;

        return $this;
    }

    /**
     * Get presenterName
     *
     * @return string 
     */
    public function getPresenterName()
    {
        return $this->presenterName;
    }

    /**
     * Set presenterSurname
     *
     * @param string $presenterSurname
     * @return Presentation
     */
    public function setPresenterSurname($presenterSurname)
    {
        $this->presenterSurname = $presenterSurname;

        return $this;
    }

    /**
     * Get presenterSurname
     *
     * @return string 
     */
    public function getPresenterSurname()
    {
        return $this->presenterSurname;
    }

    /**
     * Set presentationName
     *
     * @param string $presentationName
     * @return Presentation
     */
    public function setPresentationName($presentationName)
    {
        $this->presentationName = $presentationName;

        return $this;
    }

    /**
     * Get presentationName
     *
     * @return string 
     */
    public function getPresentationName()
    {
        return $this->presentationName;
    }

    /**
     * Set votingEnabled
     *
     * @param boolean $votingEnabled
     * @return Presentation
     */
    public function setVotingEnabled($votingEnabled)
    {
        $this->votingEnabled = $votingEnabled;

        return $this;
    }

    /**
     * Get votingEnabled
     *
     * @return boolean 
     */
    public function getVotingEnabled()
    {
        return $this->votingEnabled;
    }

    /**
     * Add vote
     *
     * @param \Netgen\LiveVotingBundle\Entity\Vote $vote
     * @return Presentation
     */
    public function addVote(\Netgen\LiveVotingBundle\Entity\Vote $vote)
    {
        $this->vote[] = $vote;

        return $this;
    }

    /**
     * Remove vote
     *
     * @param \Netgen\LiveVotingBundle\Entity\Vote $vote
     */
    public function removeVote(\Netgen\LiveVotingBundle\Entity\Vote $vote)
    {
        $this->vote->removeElement($vote);
    }

    /**
     * Get vote
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set event
     *
     * @param \Netgen\LiveVotingBundle\Entity\Event $event
     * @return Presentation
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
}
