<?php

namespace Netgen\LiveVotingBundle\Entity;

/**
 * UserEventAssociation
 */
class UserEventAssociation
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Event
     */
    private $event;


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
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Event $event
     * @return $this
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }


    public function getEvent()
    {
        return $this->event;
    }
}

