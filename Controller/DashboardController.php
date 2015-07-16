<?php

/*
 * This file is part of the Netgen LiveVoting bundle.
 *
 * https://github.com/netgen/LiveVotingBundle
 *
 */

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class DashboardController extends Controller{

  public function indexAction(){

    return $this->render(
      'LiveVotingBundle:Dashboard:index.html.twig'
    );
  }

  public function getLiveScheduleAction(){

    $presentations = $this->getDoctrine()->getManager()
          ->createQuery("
            SELECT p
            FROM LiveVotingBundle:presentation p
            WHERE :datetime > p.begin
              AND :datetime < p.end
          ")->setParameter('datetime', new \DateTime())->getArrayResult();

      $responseArray['presentations'] = $presentations;
      return new JsonResponse($responseArray);
  }

  public function getPresentationsAction(){
    $event = $this->getDoctrine()->getManager()->createQuery(
            'SELECT e
            FROM LiveVotingBundle:Event e
            WHERE :datetime > e.begin
              AND :datetime < e.end
              AND e.event IS NOT null
            ')->setParameter('datetime', new \DateTime())->getResult();

    $presentations = $this->getDoctrine()->getManager()
          ->createQuery("
            SELECT p
            FROM LiveVotingBundle:Presentation p
            JOIN LiveVotingBundle:Event e
            WHERE p.event = e
             AND e = :event
            ")->setParameter('event', $event)->getArrayResult();

    foreach ($presentations as &$presentation) {
      $votes = $this->getDoctrine()->getRepository('LiveVotingBundle:Vote')
                    ->findByPresentation($presentation['id']);
      $numOfUsers = 0;
      $sum = 0;
      foreach ($votes as $vote) {
        $numOfUsers++;
        $sum += $vote->getRate();
      }
      if($numOfUsers === 0)
        $presentation['average'] = 0;
      else
        $presentation['average'] = $sum/$numOfUsers;
    }

    $responseArray['presentations'] = $presentations;
    return new JsonResponse($responseArray);
  }
}
