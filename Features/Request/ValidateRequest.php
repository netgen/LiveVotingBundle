<?php

namespace Netgen\LiveVotingBundle\Features\Request;

use Doctrine\ORM\EntityManager;
use Netgen\LiveVotingBundle\Entity\Presentation;
use Symfony\Component\HttpFoundation\RequestStack;
use Netgen\LiveVotingBundle\Exception\JsonException;

use Netgen\LiveVotingBundle\Entity\Vote;

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
            $response['seconds'] = $date_when_voting_ends - time();
            if ( time()>$date_when_voting_ends ){
                $response['error']=1;
                $response['errorMessage'] = 'Voting is closed.';
                // not returning because we still need to send presentations
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
            $tmp = $this->getPresentationArray($presentation, $rate);
            array_push($response['presentations'], $tmp);
        }
        return $response;
    }

    protected function getPresentationArray(Presentation $presentation, $rate){
        return array(
            'presenterName' => $presentation->getPresenterName(),
            'presenterSurname' => $presentation->getPresenterSurname(),
            'presentationName' => $presentation->getPresentationName(),
            'votingEnabled' => $presentation->getVotingEnabled(),
            'presentationId' => $presentation->getId(),
            'presenterRate' => $rate
        );
    }

    public function validateVote($presentation_id){
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();
        $rate = $request->request->get('rate');

        if(!is_numeric($rate)){
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Vote must be a number.'
            ));
        }
        $rate = intval($rate);

        $presentation = $this->em->getRepository('LiveVotingBundle:Presentation')->find($presentation_id);
        if( $presentation==null ){
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Unknown presentation.'
            ));
        }
        if( $presentation->getEvent()->getStateName()=='PRE' ){ // voting is closed
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Voting is disabled.'
            ));
        }elseif( $presentation->getEvent()->getStateName()=='POST' ){ // voting ended
            $date_when_voting_ends = intval($presentation->getEvent()->getStateValue());
            if ( time()>$date_when_voting_ends ){
                throw new JsonException(array(
                    'error'=>1,
                    'errorMessage'=>'Voting is closed.'
                ));
            }
        }

        if( $rate<=0 or $rate>5){
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Vote must be between 1 and 5.'
            ));
        }

        $user = $this->em->getRepository('LiveVotingBundle:User')->find($session->getId());
        if( $user==null ){
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Unknown user. Please enable cookies.'
            ));
        }


        $vote = $this->em->getRepository('LiveVotingBundle:Vote')->findOneBy(array(
            'user'=>$user,
            'presentation'=>$presentation
        ));

        if( $vote==null){
            $vote = new Vote();
            $vote->setUser($user);
            $vote->setPresentation($presentation);
        }
        $vote->setRate($rate);
        $vote->setEvent($presentation->getEvent());
        $em = $this->em;
        $em->persist($vote);
        $em->flush();
        return array(
            'error'=>0,
            'errorMessage'=>'Thanks for voting!'
        );
    }

}