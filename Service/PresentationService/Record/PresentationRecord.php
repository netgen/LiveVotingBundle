<?php

namespace Netgen\LiveVotingBundle\Service\PresentationService\Record;


use DateTime;

class PresentationRecord {

    private $id;

    private $event_id;

    private $user_id;

    private $name = "";

    private $description = "";

    private $hall = "";

    private $begin;

    private $end;

    private $votingEnabled = false;

    private $joind_in_id;

    private $image_url;

    private $global_brake = false;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEventId()
    {
        return $this->event_id;
    }

    /**
     * @param mixed $event_id
     */
    public function setEventId($event_id)
    {
        $this->event_id = $event_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getHall()
    {
        return $this->hall;
    }

    /**
     * @param string $hall
     */
    public function setHall($hall)
    {
        $this->hall = $hall;
    }

    /**
     * @return mixed
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * @param mixed $begin
     */
    public function setBegin(DateTime $begin)
    {
        $this->begin = $begin;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     */
    public function setEnd(DateTime $end)
    {
        $this->end = $end;
    }

    /**
     * @return boolean
     */
    public function isVotingEnabled()
    {
        return $this->votingEnabled;
    }

    /**
     * @param boolean $votingEnabled
     */
    public function setVotingEnabled($votingEnabled)
    {
        $this->votingEnabled = $votingEnabled;
    }

    /**
     * @return mixed
     */
    public function getJoindInId()
    {
        return $this->joind_in_id;
    }

    /**
     * @param mixed $joind_in_id
     */
    public function setJoindInId($joind_in_id)
    {
        $this->joind_in_id = $joind_in_id;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * @param mixed $image_url
     */
    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;
    }

    /**
     * @return boolean
     */
    public function isGlobalBrake()
    {
        return $this->global_brake;
    }

    /**
     * @param boolean $global_brake
     */
    public function setGlobalBrake($global_brake)
    {
        $this->global_brake = $global_brake;
    }





}