<?php

namespace Netgen\LiveVotingBundle\Features\Request;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Netgen\LiveVotingBundle\Exception\JsonException;

class ValidateRequest{

    protected $requestStack, $em;

    public function __construct(RequestStack $requestStack, EntityManager $em){
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function validateEventStatus($event_id){
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $event = $this->em->getRepository('LiveVotingBundle:Event')->find($event_id);
        if(!$event)
            throw new JsonException(array('error'=>2, 'errorMessage'=>'Non existing event.'));

        $user = $this->em->getRepository('LiveVotingBundle:User')->find($session->getId());
        if(!$user)
            throw new JsonException( array('error'=>2, 'errorMessage'=>'Please reload and enable cookies.') );

        $response = array(
            'error' => 0,
            'eventName' => $event->getName(),
            'eventId '=> $event->getId(),
            'eventStatus' => $event->getStateName()
        );

        if($event->getStateName()=='POST'){

            $date_when_voting_ends = intval($event->getStateValue());
            if ( time()>$date_when_voting_ends ){
                $response['error']=1;
                $response['errorMessage'] = 'Voting is closed.';
                $response['seconds']=-1;
                //return new JsonResponse($response);
            }else{
                $response['seconds'] = $date_when_voting_ends - time();
            }

        }elseif($event->getStateName()=='PRE'){
            $response['error']=1;
            $response['errorMessage'] = 'Waiting for voting to start.';
            throw new JsonException($response);
        }

        // fetching presentations for current event
        $presentations = $this->em
            ->getRepository('LiveVotingBundle:Presentation')
            ->findBy(array('event'=>$event));

        $response['presentations']=array();
        // adding presentations to response array
        $em = $this->em->getRepository('LiveVotingBundle:Vote');
        foreach($presentations as $presentation){
            $vote = $em->findOneBy(array('user'=>$user, 'presentation'=>$presentation));
            $rate = 0;
            if($vote)$rate = $vote->getRate();
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
        return $response;
    }

}