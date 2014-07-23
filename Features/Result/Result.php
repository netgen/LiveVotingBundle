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
            // TODO: Ask Edi what to return to controller and how to handle exceptions
        }

        $votes = $this->em->getRepository('LiveVotingBundle:Vote')->findByEvent($event);
        $presentations = $this->em->getRepository('LiveVotingBundle:Presentation')->findByEvent($event);
        $groupedVotes = array(); // Grouped votes by user
        foreach($votes as $vote){

            if( isset($groupedVotes[$vote->getUser()->getId()]) ){
                array_push($groupedVotes[$vote->getUser()->getId()], $vote);
            }else{
                $groupedVotes[$vote->getUser()->getId()] = array($vote);
            }

        }
        $result = array('event'=>$event);
        $animation_data = array(); // array of animation keyframes
        $presentationResult = array();
        foreach($presentations  as $presentation){
            $presentationResult[$presentation->getId()] = array(
                'numOfUsers'=>0,
                'score'=>0,
                'average'=>0,
                'presentation'=>$presentation->getPresentationName(),
                'presenter'=>array(
                    'name'=>$presentation->getPresenterName(),
                    'surname'=>$presentation->getPresenterSurname()
                )

            );
        }
        foreach($groupedVotes as $userId=>$votes){
            // $result[$userId] = $presentationResult;
            $tmp1 = $presentationResult;
            foreach($votes as $vote){
                $tmp = $presentationResult[$vote->getPresentation()->getId()];
                $tmp['numOfUsers']++;
                $tmp['score'] += $vote->getRate();
                $tmp['average'] = round($tmp['score']/$tmp['numOfUsers'], 5);
                $tmp1[$vote->getPresentation()->getId()]  = $tmp;
                $presentationResult[$vote->getPresentation()->getId()] = $tmp;
            }
            $animation_data[] = $tmp1;

        }
        $result['animation_data']=$animation_data;
        return $result;
    }

    public function getLiveResults($event_id){
        $event = $this->em->getRepository('LiveVotingBundle:Event')->find($event_id);
        if( !$event instanceof Event){
            // TODO: Ask Edi what to return to controller and how to handle exceptions
        }

        $result = array();
        $presentations = $this->em->getRepository('LiveVotingBundle:Presentation')->findBy(array(
            'event'=>$event,
            // 'votingEnabled'=>true
        ));
        foreach($presentations as $presentation){
            $votes = $presentation->getVotes();
            $data = array('numOfUsers'=>0, 'score'=>0, 'average'=>0);
            foreach($votes as $vote){
                $data['score']+=$vote->getRate();
                $data['numOfUsers']++;
            }
            if($data['numOfUsers']){
                $data['average'] = round($data['score']/$data['numOfUsers'], 5);
                $result['presentations'][] = array(
                'presentation'=>array('name'=>$presentation->getPresentationName()),
                'score'=>$data);
            }
        }
        $result['event'] = $event;
        return $result;
    }
} 