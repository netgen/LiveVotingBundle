<?php

namespace Netgen\LiveVotingBundle\Features\Result;

use Doctrine\ORM\EntityManager;
use Netgen\LiveVotingBundle\Entity\Event;
use Netgen\LiveVotingBundle\Entity\Vote;

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

        $result = array();

        $votes = $this->em->getRepository('LiveVotingBundle:Vote')->findByEvent($event);
        $groupedUsers = array();
        foreach($votes as $vote){

            if( isset($groupedUsers[$vote->getUser()->getId()] ){

            }else{
                $groupedUsers[$vote->getUser()->getId()] = array($vote);
            }

        }

        foreach($groupedUsers as $user){

        }
        return $groupedUsers;
    }
} 