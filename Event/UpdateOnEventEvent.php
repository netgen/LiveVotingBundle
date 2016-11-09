<?php

namespace Netgen\LiveVotingBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class UpdateOnEventEvent extends Event
{
    /**
     * @var integer
     */
    private $eventId;

    /**
     * UpdateOnEventEvent constructor.
     * @param $eventId
     */
    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }


}