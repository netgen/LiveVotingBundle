<?php

namespace Netgen\LiveVotingBundle\Controller;

use Netgen\LiveVotingBundle\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller {

    // TODO: clean code and add comments.
    public function eventStatusAction(Request $request, $event_id){
        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);

        if(!$event instanceof Event)
            return new JsonResponse( array('error'=>1, 'errorMessage'=>'Non existing event.') );

        $response = array(
            'error'=>0,
            'eventName' => $event->getName(),
            'eventId '=> $event->getId(),
            'eventStatus' => $event->getStateName()
        );

        if($event->getStateName()=='POST'){

            $date_when_voting_ends = intval($event->getStateValue());
            if ( time()>$date_when_voting_ends ){
                $response['error']='1';
                $response['errorMessage'] = 'Voting is closed.';
                return new JsonResponse($response);

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
        foreach($presentations as $presentation){
            $tmp = array(
                'presenterName' => $presentation->getPresenterName(),
                'presenterSurname' => $presentation->getPresenterSurname(),
                'presentationName' => $presentation->getPresentationName(),
                'votingEnabled' => $presentation->getVotingEnabled(),
                'presentationId' => $presentation->getId()
                // TODO: Also add votes that user gave for that presentation
            );
            array_push($response['presentations'], $tmp);
        }

        return new JsonResponse($response);

    }

} 