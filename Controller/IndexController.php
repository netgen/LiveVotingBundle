<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use Netgen\LiveVotingBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IndexController extends Controller
{
    /* Created so we don't mess with each other methods */
    public function indexAction(){
        throw new NotFoundHttpException('Haaaai guuurl.');
        return $this->render('LiveVotingBundle:Index:index.html.twig');
    }
}
