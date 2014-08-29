<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Netgen\LiveVotingBundle\Entity\User;
use Netgen\LiveVotingBundle\Entity\Vote;
use Netgen\LiveVotingBundle\Entity\Event;


class PrizeDrawController extends Controller {

	public function indexAction(){

		$events = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->findAll();

		return $this->render('LiveVotingBundle:PrizeDraw:index.html.twig',
            array(
            	'events' => $events
            	)
        );
	}

	public function generatePoolAction(Request $request){

		$allVotes = $this->getDoctrine()->getRepository('LiveVotingBundle:Vote')->findAll();
		$votePool = array();

		if(!empty($_POST['check_list'])){

			foreach($_POST['check_list'] as $voteId) {
				
				for($i = 0; $i < count($allVotes); $i++) {

					if ( $allVotes[$i]->getPresentation()->getEvent()->getId() == $voteId ){

						//TODO: Kada budu valjani emailovi u bazi promjeniti u getEmail(). Trenutno su svi string(0)
						$votePool[] = $allVotes[$i]->getUser()->getEmail();
					}
				}	
            
        	}
		}

		return $this->render('LiveVotingBundle:PrizeDraw:generatePool.html.twig',
				array('votePool' => $votePool,
					'voteJson'=>json_encode($votePool))
			);
	}
}

?>