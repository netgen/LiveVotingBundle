<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ResultAdminController extends Controller{

    public function ViewResultsAction(Request $request, $event_id){
        $results = $this->get('live_voting.result')->getResults($event_id);

        return $this->render('LiveVotingBundle:Result:index.html.twig', array('animation_data'=>
            json_encode($results)));
    }

}

?>