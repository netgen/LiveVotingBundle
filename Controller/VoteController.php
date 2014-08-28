<?php


namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Netgen\LiveVotingBundle\Exception\JsonException;
use Netgen\LiveVotingBundle\Entity\Vote;

class VoteController extends Controller{

    public function voteAction(Request $request, $presentation_id){

        try{
            $rate = $request->request->get('rate');
            $presentation = $this->getDoctrine()->getRepository('LiveVotingBundle:Presentation')->find($presentation_id);
            $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')
                ->find($this->get('security.context')->getToken()->getUser()->getId());
            $event = $presentation->getEvent();

            $this->get('live_voting.handleRequest')->validateVote($presentation, $event, $user, $rate);
            $vote = $this->getDoctrine()->getRepository('LiveVotingBundle:Vote')->findOneBy(array(
                'user'=>$user,
                'presentation'=>$presentation
            ));

            // new vote
            if(!$vote){
                $vote = new Vote();
                $vote->setUser($user);
                $vote->setPresentation($presentation);
                $vote->setEvent($presentation->getEvent());
            }
            // saving vote
            $vote->setRate($rate);
            $em = $this->getDoctrine()->getManager();
            $em->persist($vote);
            $em->flush();
            $result = array(
                'error'=>0,
                'errorMessage'=>'Thanks for voting!'
            );
            return new JsonResponse($result);
        }catch(JsonException $e){
            return new JsonResponse(unserialize($e->getMessage()));
        }

    }

} 