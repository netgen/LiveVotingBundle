<?php

namespace Netgen\LiveVotingBundle\Features\Result;

use Doctrine\ORM\EntityManager;
use Netgen\LiveVotingBundle\Entity\Event;


class Result {
    protected $em;

    public function __construct(EntityManager $em){
        $this->em = $em;
    }

    public function getResults($event_id){
        $event = $this->em->getRepository('LiveVotingBundle:Event')->find($event_id);
        if( !$event instanceof Event){
            // TODO: Ask Edi how what to return to controller and how to handle exceptions
        }

        // TODO: Fix database so I can fetch using only $event->getPresentations();
        // TODO: Add ability to get all votes for one event $votes = $vote->getByEvent($event);
        $result = array();
        $presentations = $this->em->
            getRepository('LiveVotingBundle:Presentation')->
            findBy(array(
                'event'=>$event
            ));

        //print_r($presentations[0]);
    }
} 