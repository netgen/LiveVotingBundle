<?php

namespace Netgen\LiveVotingBundle\Features\Request;

use Netgen\LiveVotingBundle\Entity\Presentation;
use Netgen\LiveVotingBundle\Entity\Question;
use Netgen\LiveVotingBundle\Entity\User;
use Netgen\LiveVotingBundle\Exception\JsonException;
//use Symfony\Component\HttpFoundation\Response;

use Netgen\LiveVotingBundle\Entity\Event;

class ValidateRequest{

    public function validateEventStatus(Event $event, User $user){
        if(!$event)
            throw new JsonException(array('error'=>2, 'errorMessage'=>'Non existing event.'));
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
                $response['error']=0;
                $response['errorMessage'] = 'Voting is closed.';
                // not returning because we still need to send presentations
            }
        }elseif($event->getStateName()=='PRE'){
            $response['error']=0;
            $response['errorMessage'] = 'Waiting for voting to start.';
            throw new JsonException($response);
        }
        return $response;
    }

    public function validateEventQuestionStatus(Event $event, User $user){
        if(!$event)
            throw new JsonException(array('error'=>2, 'errorMessage'=>'Non existing event.'));
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
                $response['error']=0;
                $response['errorMessage'] = 'Answering for this event is closed.';
                // not returning because we still need to send presentations
            }
        }elseif($event->getStateName()=='PRE'){
            $response['error']=0;
            $response['errorMessage'] = 'Waiting for event to start.';
            throw new JsonException($response);
        }
        return $response;
    }


    public function validateVote(Presentation $presentation, Event $event, User $user, $rate){

        if(!is_numeric($rate)){
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Vote must be a number.'
            ));
        }
        $rate = intval($rate);

        if( !$presentation ){
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Unknown presentation.'
            ));
        }
        if( $event->getStateName()=='PRE' ){ // voting is closed
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Voting is disabled.'
            ));
        }elseif( $event->getStateName()=='POST' ){ // voting ended
            $date_when_voting_ends = intval($event->getStateValue());
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

        if( $user==null ){
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Unknown user. Please enable cookies.'
            ));
        }
    }

    public function validateAnswer(Question $question, Event $event, User $user, $rate){

        if(!is_numeric($rate)){
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Vote must be a number.'
            ));
        }
        $rate = intval($rate);

        if(!$question){
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Unknown question.'
            ));
        }
        if( $event->getStateName()=='PRE' ){ // voting is closed
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Answering on these questions is not yet available.'
            ));
        }elseif( $event->getStateName()=='POST' ){ // voting ended
            $date_when_voting_ends = intval($event->getStateValue());
            if ( time()>$date_when_voting_ends ){
                throw new JsonException(array(
                    'error'=>1,
                    'errorMessage'=>'Answering on these questions is disabled.'
                ));
            }
        }

        if( $rate<0 or $rate>5){
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Answer must be between 1 and 5.'
            ));
        }

        if( $user==null ){
            throw new JsonException(array(
                'error'=>1,
                'errorMessage'=>'Unknown user. Please enable cookies.'
            ));
        }


    }

}