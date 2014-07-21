<?php


namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Netgen\LiveVotingBundle\Entity\Vote;

class VoteController extends Controller{

    // TODO: This is too big. Need to make it smaller or make it like a service.
    public function voteAction(Request $request, $presentation_id){

        $session = $request->getSession();
        // $session->start();
        $rate = $request->request->get('rate');

        if(!is_numeric($rate)){
            return new JsonResponse(array(
                'error'=>4,
                'errorMessage'=>'Vote must be a number:'.$rate
            ));
        }

        $presentation = $this->getDoctrine()->getRepository('LiveVotingBundle:Presentation')->find($presentation_id);
        if( $presentation==null ){
            return new JsonResponse(array(
                'error'=>3,
                'errorMessage'=>'Unknown presentation.'
            ));
        }
        if( $presentation->getEvent()==null ){
            return new JsonResponse(array(
                'error'=>5
            ));
        }elseif( $presentation->getEvent()->getStateName()=='PRE' ){ // voting is closed
            return new JsonResponse(array(
                'error'=>1,
                'errorMessage'=>'Voting is disabled.'
            ));
        }elseif( $presentation->getEvent()->getStateName()=='POST' ){ // voting ended
            $date_when_voting_ends = intval($presentation->getEvent()->getStateValue());
            if ( time()>$date_when_voting_ends ){
                return new JsonResponse(array(
                    'error'=>1,
                    'errorMessage'=>'Voting is closed.'
                ));
            }
        }
        $rate = intval($rate);
        if( $rate<0 or $rate>5){
            return new JsonResponse(array(
                'error'=>3,
                'errorMessage'=>'Vote must be between 1 and 5.'
            ));
        }

        $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->find($session->getId());
        if( $user==null ){
            return new JsonResponse(array(
                'error'=>2,
                'errorMessage'=>'Unknown user.'
            ));
        }


        $vote = $this->getDoctrine()->getRepository('LiveVotingBundle:Vote')->findOneBy(array(
            'user'=>$user,
            'presentation'=>$presentation
        ));

        if( $vote==null){
            $vote = new Vote();
            $vote->setUser($user);
            $vote->setPresentation($presentation);
        }
        $vote->setRate($rate);
        $em = $this->getDoctrine()->getManager();
        $em->persist($vote);
        $em->flush();
        return new JsonResponse(array(
            'error'=>0,
            'errorMessage'=>'Thanks for voting!'
        ));

    }

} 