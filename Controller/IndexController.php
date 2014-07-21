<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Netgen\LiveVotingBundle\Entity\User;

class IndexController extends Controller
{
    public function indexAction()
    {
        return $this->render('LiveVotingBundle:Index:index.html.twig');
    }


    /* Created so we don't mess with each other methods */
    public function index2Action(Request $request, $event_id){

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
