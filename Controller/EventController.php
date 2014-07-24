<?php

namespace Netgen\LiveVotingBundle\Controller;

use Netgen\LiveVotingBundle\Entity\Event;
use Netgen\LiveVotingBundle\Entity\Presentation;
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
            $sessionId = $request->getSession()->getId();
            $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->find($sessionId);
            $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);

            // !! throws exception
            $response = $this->get('live_voting.handleRequest')->validateEventStatus($event, $user);

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
                if($vote)$rate = $vote->getRate();
                $tmp = $this->getPresentationArray($presentation, $rate);
                array_push($response['presentations'], $tmp);
            }
            return new JsonResponse($response);
        }catch(JsonException $exception){
            return new JsonResponse(unserialize($exception->getMessage()));
        }

    }

    protected function getPresentationArray(Presentation $presentation, $rate){
        return array(
            'presenterName' => $presentation->getPresenterName(),
            'presenterSurname' => $presentation->getPresenterSurname(),
            'presentationName' => $presentation->getPresentationName(),
            'votingEnabled' => $presentation->getVotingEnabled(),
            'presentationId' => $presentation->getId(),
            'image' =>  $presentation->getImage() ? $presentation->getWebPath() : 'img/man1.png',
            'presenterRate' => $rate
        );
    }

    public function indexAction(Request $request, $event_id){
        $session = $request->getSession();
        $session->start();
        $session_id = $session->getId();
        $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->find($session_id);

        // Saving new user to database
        if($user==null){
            $user = new User();
            $user->setId($session_id);
            $user->setEmail('');
            $user->setPassword('');
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        return $this->render('LiveVotingBundle:Index:index.html.twig');
    }

} 