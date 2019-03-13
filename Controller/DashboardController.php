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
use Netgen\LiveVotingBundle\Entity\PresentationComment;
use Netgen\LiveVotingBundle\Entity\UserEventAssociation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class DashboardController extends Controller{

  public function indexAction(){

    // Fetch first current ongoing master event
    $event = $this->getDoctrine()->getManager()->createQuery(
        'SELECT e
            FROM LiveVotingBundle:Event e
            WHERE :datetime > e.begin
              AND :datetime < e.end
              AND e.event IS NULL
            ')->setParameter('datetime', new \DateTime())->getResult();

    if (is_array($event) && !empty($event)) {
      if (reset($event) instanceof Event) {
        $name = $event[0]->getName();
      } else {
        $name = null;
      }
    } else {
      $name = null;
    }

    return $this->render(
        'LiveVotingBundle:Dashboard:index.html.twig',
        array(
          'name' => $name
        )
    );
  }

  public function getLiveScheduleAction(){
      /** @var \Netgen\LiveVotingBundle\Entity\User $user */
      $user = $this->get('security.context')->getToken()->getUser();
      if($user !== "anon." && method_exists($user, "getId")) {

          $availablePresentations = $this->getDoctrine()->getManager()->createQueryBuilder("p")
              ->select("p, v, u")
              ->from('LiveVotingBundle:presentation', 'p')
              ->leftjoin("p.votes", "v", "WITH", "v.user = :user")
              ->leftjoin("v.user", "u")
              ->where('p.begin < :datetime')
              ->andWhere('p.end > :datetime')
              ->setParameters(array('datetime' => new \DateTime(), "user" => $user->getId()))
              ->getQuery()
              ->getArrayResult();

          $userEventAssociations = $user->getEventAssociations()->toArray();

          $events = array_map(function(UserEventAssociation $userEventAssociation){
              return $userEventAssociation->getEvent();
          }, $userEventAssociations);

          $checkPresentations = [];

          /** @var Event $event */
          foreach ($events as $event) {
              $eventPresentations = $event->getPresentations();

              foreach ($eventPresentations as $eventPresentation) {
                  $checkPresentations[] = $eventPresentation->getId();
              }
          }

          $presentations = [];

          foreach ($availablePresentations as $availablePresentation) {
              if (in_array($availablePresentation['id'], $checkPresentations)) {
                  $presentations[] = $availablePresentation;
              }
          }

      } else {
          $presentations = $this->getDoctrine()->getManager()
              ->createQuery("
            SELECT p
            FROM LiveVotingBundle:presentation p
            WHERE :datetime > p.begin
              AND :datetime < p.end
          ")->setParameter('datetime', new \DateTime())->getArrayResult();
      }
      foreach($presentations as $key => $presentation) {
          $presentations[$key]["begin"] = $presentation['begin']->format(DATE_ISO8601);
          $presentations[$key]['end'] = $presentation['end']->format(DATE_ISO8601);

      }
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

    if(is_array($event)) $event = $event[0];
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
      $presentation['votes'] = count($votes);
      foreach ($votes as $vote) {
        $numOfUsers++;
        $sum += $vote->getRate();
      }
      if($numOfUsers === 0)
        $presentation['average'] = 0;
      else
        $presentation['average'] = round($sum/$numOfUsers, 2);
    }

    // Limit LiveResults to three presentations

    // Sort by average, MAX => MIN
    usort($presentations, array($this, 'cmp'));
    //Return first three elements of array
    array_splice($presentations, 3);

    $responseArray['presentations'] = $presentations;
    return new JsonResponse($responseArray);
  }

  public function cmp($a, $b){
    if ($a['average'] == $b['average']) {
        return 0;
    }
    return ($a['average'] < $b['average']) ? 1 : -1;
  }

}
