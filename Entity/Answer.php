<?php

namespace Netgen\LiveVotingBUndle;

use Doctrine\ORM\Mapping as ORM;

class Answer
{

	private $id;
	private $question_id;
	private $user_id;
	private $answer;

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
		return $this->id;
	}

	public function getQuestionId()
	{
		return $this->question_id;
	}

	public function setQuestionId($question_id)
	{
		$this->question_id = $question_id
		return $this->question_id;
	}

	public function getUserId()
	{
		return $this->user_id;
	}

	public function setUserId($user_id)
	{
		$this->user_id = $user_id;
		return $this->user_id;
	}

	public function getAnswer()
	{
		return $this->answer;
	}

	public function setAnswer($answer)
	{
		$this->answer= $answer;
		return $this->answer;
	}


}