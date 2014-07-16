<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class PresentationController extends Controller{

	public function presentationsAction(){

		return new JsonResponse(array('test'=>'is'));

	}
}

?>