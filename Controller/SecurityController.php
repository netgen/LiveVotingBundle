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
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * User login
     * @param Request $request
     * @return mixed
     */
    public function userLoginAction(Request $request)
    {
        $error = $this->checkLoginError($request);

        return $this->render(
            'LiveVotingBundle:Security:userlogin.html.twig',
            array(
                'error' =>  $error
            )
        );
    }

    /**
     * Admin login
     * @param Request $request
     * @return mixed
     */
    public function adminLoginAction(Request $request){
        $error = $this->checkLoginError($request);

        return $this->render(
          'LiveVotingBundle:Security:adminlogin.html.twig',
          array(
            'error' => $error
          )
        );
    }

    public function checkLoginError(Request $request){

      $session = $request->getSession();
      // get the login error if there is one
      if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
          $error = $request->attributes->get(
              SecurityContextInterface::AUTHENTICATION_ERROR
          );
          return $error;
      } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
          $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
          $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
          return $error;
      } else {
          $error = '';
          return $error;
      }

    }
}
