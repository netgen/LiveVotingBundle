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
            WHERE CURRENT_TIME() > p.begin
              AND CURRENT_TIME() < p.end
          ")->getArrayResult();

      $responseArray['presentations'] = $presentations;
      return new JsonResponse($responseArray);
  }

  public function getPresentationsAction(){
    $presentations = $this->getDoctrine()->getManager()
          ->createQuery("
            SELECT p
            FROM LiveVotingBundle:Vote v
            JOIN LiveVotingBundle:Presentation p
            WHERE v.presentation = p
            GROUP BY p.id
            ")->getArrayResult();

    foreach ($presentations as &$presentation) {
      $votes = $this->getDoctrine()->getRepository('LiveVotingBundle:Vote')
                    ->findByPresentation($presentation['id']);
      $numOfUsers = 0;
      $sum = 0;
      foreach ($votes as $vote) {
        $numOfUsers++;
        $sum += $vote->getRate();
      }
      $presentation['average'] = $sum/$numOfUsers;
    }

    $responseArray['presentations'] = $presentations;
    return new JsonResponse($responseArray);
  }
}
