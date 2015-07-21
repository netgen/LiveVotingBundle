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

/**
 * Result controller. (user)
 */
class ResultController extends Controller{

    /**
     * Display live results on /user/live_results/{event_id} page
     * @param $event_id Event ID
     */
    public function indexAction($event_id)
    {
        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);
        $enabled = $event->getallowViewingResults();
        if($enabled)
        {
            return $this->render('LiveVotingBundle:Result:liveIndex.html.twig', array(
                'event'=>$event,
                'live_results_url'=>$this->generateUrl('result_json', array('event_id'=>$event_id))
            ));
        }

        else
        {
            return $this->render('LiveVotingBundle:Result:empty.html.twig');
        }
    }

    /**
     * Returns json for all presentations so javascript can draw it
     * @param $request Request
     * @param $event_id Event ID
     * @return JSON response of all presenatations (score)
     */
    public function getResultsAction(Request $request, $event_id)
    {
        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);
        $enabled = $event->getallowViewingResults();

        if($enabled)
        {
            $results = $this->get('live_voting.result')->getLiveResults($event_id);

            // Limit LiveResults to three presentations

            // Sort by average, MAX => MIN
            usort($results['presentations'], array($this, 'cmp'));
            //Return first three elements of array
            array_splice($results['presentations'], 3);

            return new JsonResponse($results);
        }

        else
        {
            return new JsonResponse(array());
        }
    }

    public function cmp($a, $b){
      if ($a['score']['average'] == $b['score']['average']) {
          return 0;
      }
      return ($a['score']['average'] < $b['score']['average']) ? 1 : -1;
    }

    /**
     * Returns tabel of results for presentations
     * @param $event_id Event ID
     */
    public function getTableAction($event_id){
        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);
        $enabled = $event->getallowViewingResults();

        if($enabled)
        {
            $results = $this->get('live_voting.result')->getLiveResults($event_id);

            usort($results['presentations'], function($v1, $v2){
                $v1score = floatval($v1['score']['average']);
                $v2score = floatval($v2['score']['average']);
                if($v1score>$v1score)return -1;
                if($v1score<$v2score)return 1;
                return 0;
            });

            return $this->render('LiveVotingBundle:Result:table.html.twig', array(
                'presentations'=>$results
            ));
        }

        else
        {
            return $this->render('LiveVotingBundle:Result:empty.html.twig');
        }
    }
}

?>
