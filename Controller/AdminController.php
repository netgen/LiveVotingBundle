<?php

/*
 * This file is part of the Netgen LiveVoting bundle.
 *
 * https://github.com/netgen/LiveVotingBundle
 *
 */

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Landing admin controller
 */
class AdminController extends Controller{

    public function indexAction()
    {
        return $this->render('LiveVotingBundle:Index:admin.html.twig');
    }
}
