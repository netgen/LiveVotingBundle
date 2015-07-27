<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 7/13/15
 * Time: 11:15 AM
 */

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;

use Netgen\LiveVotingBundle\Entity\User;
use Netgen\LiveVotingBundle\Entity\Registration;
use Netgen\LiveVotingBundle\Form\UserDataType;
use Netgen\LiveVotingBundle\Form\RegistrationUserType;

use Netgen\LiveVotingBundle\Form\PresentationCommentType;
use Netgen\LiveVotingBundle\Entity\PresentationComment;

use Netgen\LiveVotingBundle\Form\PresentationImageType;
use Netgen\LiveVotingBundle\Entity\PresentationImage;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller {

    public function indexAction(){

        $user_id = $this->getUser()->getId();
        //dump($user_id);die;

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LiveVotingBundle:User')->find($user_id);
        $entities = $em->getRepository('LiveVotingBundle:User')->findById($user_id);
        //dump($entities);die;
        $that = $this;

        return $this->render('LiveVotingBundle:User:useredit.html.twig', array(
            'entities' => array_map(
                function ($ent) use ($that) {
                    return array($ent, $that->createEnableDisableForm($ent)->createView());
                }, $entities),
            'user' => $user
        ));
    }

    public function createEnableDisableForm(Presentation $entity){
        $form = $this->createFormBuilder();
        $form->setMethod('PUT');
        $form->setAction($this->generateUrl('admin_presentation_vote_enable', array('id'=>$entity->getId())));
        if($entity->getVotingEnabled()==False)
            $form->add('disable', 'submit',  array('label'=>'Disabled', 'attr'=>array('class'=>'btn btn-danger')));
        else
            $form->add('enable', 'submit',  array('label'=>'Enabled', 'attr'=>array('class'=>'btn btn-success')));

        return $form->getForm();
    }

    private function createEditForm(User $entity, Registration $entity2)
    {
        $form = $this->createFormBuilder()
                ->add('userInfo', new UserDataType(), array('data'=>$entity))
                ->add('registrationInfo', new RegistrationUserType(), array('data'=>$entity2))
                ->add('submit', 'submit', array('label'=>'update'))
        ;

        return $form->getForm();
    }



    public function EditAction()
    {
        $cookie = $this->getRequest()->cookies->get('userEditEnabled');

        if($cookie !== '1'){
          return new Response('You have no permission to edit data.');
        }

        $user_id =$this->getUser()->getId();

        $em = $this->getDoctrine()->getManager();

        if (count($em->getRepository('LiveVotingBundle:Registration')->findByUser($this->getUser()))==0) {
            /*throw $this->createNotFoundException('Unable to find Event registration.');*/
            return new Response('There is no registration for any event!s');
        }
        $entity = $em->getRepository('LiveVotingBundle:User')->find($user_id);
        $entity2= $em->getRepository('LiveVotingBundle:Registration')->findByUser($this->getUser())[0];
        //dump($entity2);die;
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity or Registration.');
        }

        $form = $this->createEditForm($entity, $entity2);

        return $this->render('LiveVotingBundle:User:useredit.html.twig', array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:User')->find($this->getUser()->getId());
        $entity2= $em->getRepository('LiveVotingBundle:Registration')->findByUser($this->getUser())[0];

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $form = $this->createEditForm($entity, $entity2);
        $form->handleRequest($request);
        if($form->isValid()){
          $entity = $form->getData()['userInfo'];
          $entity2 = $form->getData()['registrationInfo'];
          $em->persist($entity);
          $em->persist($entity2);
          $em->flush();

          $request->getSession()->getFlashBag()->add(
            'message', 'Info updated.'
          );

          return $this->redirect($this->generateUrl('user_edit'));
        }

        return $this->render('LiveVotingBundle:User:useredit.html.twig', array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        ));
    }

    public function commentEventAction($eventId){

    }

    public function commentPresentationAction(Request $request, $presentationId){
      $em = $this->getDoctrine()->getManager();

      $user = $em->getRepository('LiveVotingBundle:User')->find($this->getUser()->getId());
      $presentation = $em->getRepository('LiveVotingBundle:Presentation')->findById($presentationId)[0];

      $entity = new PresentationComment();
      $form = $this->createForm(new PresentationCommentType(), $entity, array(
          'method' => 'POST',
          'action' => $this->generateUrl('user_comment_presentation', array('presentationId'=>$presentationId))
      ));

      $form->add('submit', 'submit', array('label' => 'Comment'));
      $form->handleRequest($request);

      if($form->isValid()){
        $entity->setUser($user);
        $entity->setPresentation($presentation);
        $entity->setPublished(new \DateTime());

        $em->persist($entity);
        $em->flush();
        return new JsonResponse(array(
            "content" => $entity->getContent(),
            "published_at" => $entity->getPublished()->format(DATE_ISO8601),
            "user_display_name" =>
                $entity->getUser()->getEmail() ?
                    substr($entity->getUser()->getEmail(), 0, strrpos($entity->getUser()->getEmail(), "@"))
                    :
                    $entity->getUser()->getUsername()
        ));
      }
    }

    public function uploadImagePresentationAction(Request $request, $presentationId){
      $em = $this->getDoctrine()->getManager();

      $user = $em->getRepository('LiveVotingBundle:User')->find($this->getUser()->getId());
      $presentation = $em->getRepository('LiveVotingBundle:Presentation')->findById($presentationId)[0];

      $entity = new PresentationImage();
      $form = $this->createForm(new PresentationImageType(), $entity, array(
          'method' => 'POST',
          'action' => $this->generateUrl('user_upload_image_presentation', array('presentationId'=>$presentationId))
      ));

      $form->add('submit', 'submit', array('label' => 'Post image'));

      $form->handleRequest($request);

      if($form->isValid()){
        $entity->upload();
        $em->persist($entity);
        $em->flush();
        return new JsonResponse(array('success' => true));
      }
    }

}
