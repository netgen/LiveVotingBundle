<?php

namespace Netgen\LiveVotingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class Answer
{
	/**
	* @var integer
	*/
	private $id;

	/**
	* @var integer
	*/
	private $answer;

	/**
     * @var \Netgen\LiveVotingBundle\Entity\Presentation
     */
    private $question;

    /**
     * @var \Netgen\LiveVotingBundle\Entity\User
     */
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
	* Set id
	*
	* @param integer $id
	* @return Answer
	*/
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	* Get Answer
	*
	* @return integer
	*/
	public function getAnswer()
	{
		return $this->answer;
	}

	/**
	* Set Answer
	*
	* @param integer $answer
	* @return Answer
	*/
	public function setAnswer($answer)
	{
		$this->answer= $answer;
		return $this;
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
     * Set question
     *
     * @param \Netgen\LiveVotingBundle\Entity\Quesiton $question
     * @return Answer
     */
	public function setQuestion(\Netgen\LiveVotingBundle\Entity\Presentation $presentation = null)
    {
        $this->question = $question;

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




}