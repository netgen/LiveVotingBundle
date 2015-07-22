<?php

/*
 * This file is part of the Netgen LiveVoting bundle.
 *
 * https://github.com/netgen/LiveVotingBundle
 *
 */

namespace Netgen\LiveVotingBundle\Controller;

use Netgen\LiveVotingBundle\Entity\Event;
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
    /**
     * Lists all Events on /user/ landing page
     * @return mixed
     */
    public function landingAction()
    {
        $events = $this->getDoctrine()->getManager()
            ->createQueryBuilder("e")
            ->select("e")
            ->from("LiveVotingBundle:event", "e")
            ->where('e.begin < :datetime')
            ->andWhere('e.end > :datetime')
            ->orderBy('e.event', 'ASC')
            ->setParameter('datetime', new \DateTime())
            ->getQuery()->getResult();
        $presentations = $this->getDoctrine()->getRepository('LiveVotingBundle:Presentation')->findByUser($this->getUser());


        return $this->render('LiveVotingBundle:Index:landing.html.twig',
            array(
              'events'=>$events,
              'userPresentationsNum' => count($presentations)
              )
        );
    }
}
