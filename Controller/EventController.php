<?php

namespace Netgen\LiveVotingBundle\Controller;

use Netgen\LiveVotingBundle\Entity\Event;
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
            $result = $this->get('live_voting.handleRequest')->validateEventStatus($event_id);
            return new JsonResponse($result);
        }catch(JsonException $exception){
            return new JsonResponse(unserialize($exception->getMessage()));
        }

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