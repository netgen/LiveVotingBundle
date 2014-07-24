<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResultController extends Controller{

    /*
     * Displays only html
     */
    public function indexAction($event_id){
        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);
        $enabled = $event->getallowViewingResults();
        if($enabled){
            return $this->render('LiveVotingBundle:Result:liveIndex.html.twig', array(
                'event'=>$event,
                'live_results_url'=>$this->generateUrl('result_json', array('event_id'=>$event_id))
            ));
        }else{
            return $this->render('LiveVotingBundle:Result:empty.html.twig');
        }
    }

    /*
     * Returns json for all presentations so javascript can draw it
     */
    public function getResultsAction(Request $request, $event_id){
        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);
        $enabled = $event->getallowViewingResults();
        if($enabled){
            $results = $this->get('live_voting.result')->getLiveResults($event_id);
            return new JsonResponse($results);
        }else{
            return new JsonResponse(array());
        }
    }

    public function getTableAction($event_id){
        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);
        $enabled = $event->getallowViewingResults();
        if($enabled){
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
        }else{
            return $this->render('LiveVotingBundle:Result:empty.html.twig');
        }
    }

}

?>