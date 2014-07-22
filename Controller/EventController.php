<?php

namespace Netgen\LiveVotingBundle\Controller;

use Netgen\LiveVotingBundle\Entity\Event;
use Netgen\LiveVotingBundle\Entity\User;
use Netgen\LiveVotingBundle\Entity\Vote;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller {

    // TODO: clean code and add comments.
    public function eventStatusAction(Request $request, $event_id){
        $session = $request->getSession();
        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);

        if(!$event instanceof Event)
            return new JsonResponse( array('error'=>1, 'errorMessage'=>'Non existing event.') );

        $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->find($session->getId());
        if(!$user instanceof User)
            return new JsonResponse( array('error'=>1, 'errorMessage'=>'Please reload and enable cookies.') );

        $response = array(
            'error' => 0,
            'eventName' => $event->getName(),
            'eventId '=> $event->getId(),
            'eventStatus' => $event->getStateName()
        );

        if($event->getStateName()=='POST'){

            $date_when_voting_ends = intval($event->getStateValue());
            if ( time()>$date_when_voting_ends ){
                $response['error']='1';
                $response['errorMessage'] = 'Voting is closed.'.time()." ".$date_when_voting_ends;
                //return new JsonResponse($response);
            }else{
                $response['seconds'] = $date_when_voting_ends - time();
            }

        }elseif($event->getStateName()=='PRE'){
            $response['error']='1';
            $response['errorMessage'] = 'Waiting for voting to start.';
            return new JsonResponse($response);
        }

        // fetching presentations for current event
        $presentations = $this->getDoctrine()
            ->getRepository('LiveVotingBundle:Presentation')
            ->findBy(array('event'=>$event));

        $response['presentations']=array();
        // adding presentations to response array
        $em = $this->getDoctrine()->getRepository('LiveVotingBundle:Vote');
        foreach($presentations as $presentation){
            $vote = $em->findOneBy(array('user'=>$user, 'presentation'=>$presentation));
            $rate = 0;
            if($vote instanceof Vote)$rate = $vote->getRate();
            $tmp = array(
                'presenterName' => $presentation->getPresenterName(),
                'presenterSurname' => $presentation->getPresenterSurname(),
                'presentationName' => $presentation->getPresentationName(),
                'votingEnabled' => $presentation->getVotingEnabled(),
                'presentationId' => $presentation->getId(),
                'presenterRate' => $rate
            );
            array_push($response['presentations'], $tmp);
        }

        return new JsonResponse($response);

    }

} 