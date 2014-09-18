<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Netgen\LiveVotingBundle\Entity\Presentation;
use Netgen\LiveVotingBundle\Form\PresentationType;

/**
 * Question controller.
 *
 */
class QuestionAdminController extends Controller
{

    /**
     * Lists all Question entities.
     *
     */
    public function indexAction($event_id)
    {
        echo("Hi");

        return $this->render('LiveVotingBundle:Presentation:index.html.twig', null);
    }

}
