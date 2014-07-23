<?php


namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Netgen\LiveVotingBundle\Exception\JsonException;
use Netgen\LiveVotingBundle\Entity\Vote;

class VoteController extends Controller{

    // TODO: This is too big. Need to make it smaller or make it like a service.
    public function voteAction(Request $request, $presentation_id){

        try{
            $result = $this->get('live_voting.handleRequest')->validateVote($presentation_id);
            return new JsonResponse($result);
        }catch(JsonException $e){
            return new JsonResponse(unserialize($e->getMessage()));
        }

    }

} 