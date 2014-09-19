<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Netgen\LiveVotingBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class QuestionController extends Controller{

	public function indexAction($event_id)
	{
		$em = $this->getDoctrine()->getManager();
		$event = $em->getRepository('LiveVotingBundle:Event')->find($event_id);

			//TODO: redirect to 404 page
		if(!$event)
		{
			throw new NotFoundHttpException("404: Page not found");
		}
		$questions = $em->getRepository('LiveVotingBundle:Question')->FindBy(array('event' => $event));

		return $this->render('LiveVotingBundle:Answer:index.html.twig', array('questions' => $questions));
	}

}