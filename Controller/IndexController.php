<?php

/*
 * This file is part of the Netgen LiveVoting bundle.
 *
 * https://github.com/netgen/LiveVotingBundle
 *
 */

namespace Netgen\LiveVotingBundle\Controller;

use Netgen\LiveVotingBundle\Entity\Event;
use Netgen\LiveVotingBundle\Entity\Presentation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Netgen\LiveVotingBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/*
 * Index landing controller. (user)
 */
class IndexController extends Controller
{
    private $has_voting = false;
    /**
     * Lists all Events on /user/ landing page
     * @return mixed
     */
    public function landingAction()
    {
        $events = $this->getDoctrine()->getManager()
            ->createQueryBuilder("e")
            ->select("e, p")
            ->from("LiveVotingBundle:Event", "e")
            ->where('e.begin < :datetime AND :datetime < e.end')
            ->orderBy('e.event', 'ASC')
            ->leftjoin('e.presentations', "p")
            ->setParameter('datetime', new \DateTime())
            ->getQuery()->getResult();
        $events = $this->sortEvents($events);

        /** @var User $currentUser */
        $currentUser = $this->get('security.context')->getToken()->getUser();

        $eventAssociations = $currentUser->getEventAssociations()->toArray();

        $eventIds = array_map(function($eventAssociation){return $eventAssociation->getEvent()->getId();}, $eventAssociations);

        $eventsForVoting = [];

        foreach($events as $key => $event) {
            /**
             * @var $event Event
             */
            $this->has_voting = false;
            foreach($event->getPresentations() as $presentationKey => $presentation) {
                /**
                 * @var $presentation Presentation
                 */
                if($presentation->getVotingEnabled()) {
                    $this->has_voting = true;
                }
            }
            $events[$key]->hasVoting = $this->has_voting;

            if (in_array($event->getId(), $eventIds)) {
                $eventsForVoting[] = $event;
            }
        }

        $event = [];

        // Fetch first current ongoing master event for the current user
        foreach ($eventsForVoting as $eventForVoting) {
            if (empty($eventForVoting->getPresentations()->toArray()) && !$eventForVoting->hasVoting) {
                $event[] = $eventForVoting;
            }
        }

        if (is_array($event) && !empty($event)) {
            if (reset($event) instanceof Event) {
                $name = $event[0]->getName();
            } else {
                $name = null;
            }
        } else {
            $name = null;
        }

        return $this->render('LiveVotingBundle:Index:landing.html.twig',
            array(
                'events'=>$eventsForVoting,
                'name' => $name
            )
        );
    }

    private function sortEvents($events)
    {
        $sortedEvents = array();
        $masterEvents = array_filter($events, function($event) {
            return $event->getEvent() == null;
        });
        foreach($masterEvents as $masterEvent) {
            array_push($sortedEvents, $masterEvent);
            foreach($events as $event) {
                if($event->getEvent() != null && $event->getEvent()->getId() == $masterEvent->getId()) {
                    array_push($sortedEvents, $event);
                }
            }
        }
        return $sortedEvents;
    }
}
