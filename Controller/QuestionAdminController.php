<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Netgen\LiveVotingBundle\Entity\Question;

/**
 * Question controller.
 *
 */
class QuestionAdminController extends Controller
{

    /**
     * Lists all Question entities.
     *
     */
    public function indexAction($event_id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('LiveVotingBundle:Event')->find($event_id);
        $entities = $em->getRepository('LiveVotingBundle:Question')->findBy(array('event'=>$event));

        $that = $this;
        return $this->render('LiveVotingBundle:Question:index.html.twig', array(
            'entities' => array_map(
                function($ent) use ($that) {
                   return array($ent, $that->createEnableDisableForm($ent)->createView());
                }, $entities),
            'event' => $event
        ));
    }

}
