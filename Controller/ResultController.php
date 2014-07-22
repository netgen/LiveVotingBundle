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
        $enabled = true;
        if($enabled){
            $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);
            return $this->render('LiveVotingBundle:Result:liveIndex.html.twig', array(
                'event'=>$event,
                'live_results_url'=>$this->generateUrl('result_json', array('event_id'=>$event_id))
            ));
        }else{
            return $this->redirect('smisli gdje'); // TODO: SMISLI
        }
    }

    /*
     * Returns json for all presentations so javascript can draw it
     */
    public function getResultsAction(Request $request, $event_id){
        $enabled = true;
        if($enabled){
            $results = $this->get('live_voting.result')->getLiveResults($event_id);
            return new JsonResponse($results);
        }else{
            return new JsonResponse(array());
        }
    }

}

?>