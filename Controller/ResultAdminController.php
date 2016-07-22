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

/**
 * Class ResultAdminController
 * @package Netgen\LiveVotingBundle\Controller
 */
class ResultAdminController extends Controller{

    /**
     * @param Request $request
     * @param Event ID $event_id
     * @return mixed
     */
    public function ViewResultsAction(Request $request, $event_id)
    {
        $results = $this->get('live_voting.result')->getResults($event_id);
        return $this->render('LiveVotingBundle:Result:index.html.twig', array(
            'animation_data' => json_encode($results),
            'event_name' => $results['event']->getName(),
            'event_id' => $results['event']->getId()
        ));

    }

}

?>