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
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\HttpFoundation\Cookie;

class SecurityController extends Controller
{
    /**
     * User login
     * @param Request $request
     * @return mixed
     */
    public function userLoginAction(Request $request, $activateHash)
    {
      if($activateHash === null){
        return $this->redirect($this->generateUrl('root'));
      }

      $validHash = false;
      $activatedUser = null;

      $users = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->findAll();

      for($i=0;$i<count($users);$i++){
        $user_email = $users[$i]->getEmail();

        $emailHash = md5($this->container->getParameter('email_hash_prefix') . $user_email);

        if($emailHash === $activateHash){
          $validHash = true;
          $activatedUser = $users[$i];
          break;
        }
      }

      if($validHash && $activatedUser){
          $secretKey = $this->container->getParameter('secret');
          $token = new RememberMeToken($activatedUser, 'user', $secretKey);
          $this->get('security.context')->setToken($token);

          $user = $activatedUser;
          $expires = time() + 60*60*24*183;
          $value = $this->generateCookieValue(get_class($user), $user->getUsername(), $expires, $user->getPassword());

          $return = $this->redirect($this->generateUrl(/*user_landing*/'question', array('event_id' => '2')));
          $return->headers->setCookie(new Cookie('userEditEnabled', '1', time()+60*60*24*90));

          $presentations = $this->getDoctrine()->getRepository('LiveVotingBundle:Presentation')->findByUser($user);
          $return->headers->setCookie(new Cookie('numOfPresentations', count($presentations), time()+60*60*24*90));

          $return->headers->setCookie(
              new Cookie(
                  'REMEMBERME',
                  $value,
                  $expires
              )
          );

          return $return;
      }else{
          return $this->render('LiveVotingBundle:Error:general.html.twig', array(
              "message" => "Activation link is invalid."
          ));
      }
    }

    public function checkEmailAction(){
      return $this->render(
        'LiveVotingBundle:Security:checkemail.html.twig'
      );
    }

    /**
     * Admin login
     * @param Request $request
     * @return mixed
     */
    public function adminLoginAction(Request $request){
        if($this->get('security.context')->isGranted('ROLE_ADMIN'))
          return $this->redirect($this->generateUrl('admin_index'));

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

    /**
    * Three methods used from Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices
    * to create cookie when the link is created. Modified to work with controller.
    */

    public function generateCookieValue($class, $username, $expires, $password)
    {
        return $this->encodeCookie(array(
            $class,
            base64_encode($username),
            $expires,
            $this->generateCookieHash($class, $username, $expires, $password)
        ));
    }

    protected function encodeCookie(array $cookieParts)
    {
        return base64_encode(implode(':', $cookieParts));
    }

    public function generateCookieHash($class, $username, $expires, $password)
    {
        return hash_hmac('sha256', $class.$username.$expires.$password, $this->container->getParameter('secret'));
    }
}
