<?php

namespace Netgen\LiveVotingBundle\Controller;

use Netgen\LiveVotingBundle\Entity\Event;
use Netgen\LiveVotingBundle\Entity\User;
use Netgen\LiveVotingBundle\Entity\Vote;
use Netgen\LiveVotingBundle\Exception\JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller {

    // TODO: clean code and add comments.
    public function eventStatusAction(Request $request, $event_id){
        try{
            $result = $this->get('live_voting.handlerequest')->validateEventStatus($event_id);
            return new JsonResponse($result);
        }catch(JsonException $exception){
            return new JsonResponse(unserialize($exception->getMessage()));
        }

    }

} 