<?php

namespace Netgen\LiveVotingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Event
 */
class Event
{

    private $id;
    private $name;
    private $stateName;
    private $stateValue = null;
    private $allowViewingResults = false;
    private $image = '';

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
     * Set image
     *
     * @param image
     */    
    public function setImage(UploadedFile $image = null)
    {
        $this->image = $image;
    }

    /**
     * Get image
     *
     * @return image
     */
    public function getImage()
    {
        return $this->image;
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

    public function __toString()
    {
        return $this->getName();
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $presentations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->presentations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add presentations
     *
     * @param \Netgen\LiveVotingBundle\Entity\Presentation $presentations
     * @return Event
     */
    public function addPresentation(\Netgen\LiveVotingBundle\Entity\Presentation $presentations)
    {
        $this->presentations[] = $presentations;

        return $this;
    }

    /**
     * Remove presentations
     *
     * @param \Netgen\LiveVotingBundle\Entity\Presentation $presentations
     */
    public function removePresentation(\Netgen\LiveVotingBundle\Entity\Presentation $presentations)
    {
        $this->presentations->removeElement($presentations);
    }

    /**
     * Get presentations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPresentations()
    {
        return $this->presentations;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $presentation;


    /**
     * Get presentation
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPresentation()
    {
        return $this->presentation;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $vote;


    /**
     * Add vote
     *
     * @param \Netgen\LiveVotingBundle\Entity\Vote $vote
     * @return Event
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $votes;


    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVotes()
    {
        return $this->votes;
    }


    public function setallowViewingResults($allowViewingResults){
        $this->allowViewingResults = $allowViewingResults;
    }

    public function getallowViewingResults(){
        return $this->allowViewingResults;
    }
}
