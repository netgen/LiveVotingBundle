<?php 

namespace Netgen\LiveVotingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/*
* Question
*/

class Question
{
	/**
     * @var integer
     */
	private $id;

    /**
     * @var boolean
     */
	private $votingEnabled = true;

    /**
     * @var integer
     */
	private $question_type;

    /**
     * @var string
     */
	private $question;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $answer;

    /**
     * @var \Netgen\LiveVotingBundle\Entity\Event
     */
    private $event;


    /**
     * Constructor
     */

    public function __construct()
    {
        $this->answer = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set id
     *
     * @return integer 
     */
	public function setId($id)
	{
		$this->id = $id;

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
     * Set votingEnabled
     *
     * @return boolean 
     */
	public function setVotingEnabled($votingEnabled)
	{
		$this->votingEnabled = $votingEnabled;

		return $this;
	}

    /**
     * Get question_type
     *
     * @return integer
     */
	public function getQuestionType()
	{
		return $this->question_type;
	}

    /**
     * Get question_type
     *
     * @return integer
     */	
	public function setQuestionType($question_type)
	{
		$this->question_type = $question_type;

		return $this;
	}

    /**
     * Get question
     *
     * @return string
     */
	public function getQuestion()
	{
		return $this->question;
	}

    /**
     * Set question
     *
     * @return string
     */
	public function setQuestion($question)
	{
		$this->question = $question;

		return $this;
	}

    /**
     * Add answer
     *
     * @param \Netgen\LiveVotingBundle\Entity\Answer $answer
     * @return Question
     */
    public function addAnswer(\Netgen\LiveVotingBundle\Entity\Answer $answer)
    {
        $this->answer[] = $answer;

        return $this;
    }

    /**
     * Remove answer
     *
     * @param \Netgen\LiveVotingBundle\Entity\Answer $answer
     */
    public function removeAnswer(\Netgen\LiveVotingBundle\Entity\Answer $answer)
    {
        $this->answer->removeElement($answer);
    }

    /**
     * Get answer
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set event
     *
     * @param \Netgen\LiveVotingBundle\Entity\Event $event
     * @return Question
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $answers;


    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }

}

?>