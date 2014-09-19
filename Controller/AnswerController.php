<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Netgen\LiveVotingBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/* privremeno */
use Symfony\Component\HttpFoundation\Response;

class AnswerController extends Controller{

	public function indexAction()
	{
		/* TODO */
		return new Response('Hello!', 200);
	}

}