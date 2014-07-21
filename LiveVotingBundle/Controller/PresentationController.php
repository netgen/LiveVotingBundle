<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use Netgen\LiveVotingBundle\Entity\User;

class PresentationController extends Controller{

	/**
	* Returns presentations to a user if they exist.
	* ALso returns status code if they can't vote because:
	* voting is over
	* still waiting for presentations to start
	*/
	public function presentationsAction(){

		/*$user = new User();
		$user->setId(strval(rand()));
		$user->setEmail('Pero');
		$user->setPassword('foobar');

		$em = $this->getDoctrine()->getEntityManager();
		$em->persist($user);
		$em->flush();
		
		return new JsonResponse(array('test'=>strval($user->getId())));*/

        return new JsonResponse(array('test'=>'123'));
	}
}

?>