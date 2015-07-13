<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 7/13/15
 * Time: 11:15 AM
 */

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Netgen\LiveVotingBundle\Entity\User;
use Netgen\LiveVotingBundle\Entity\Registration;
use Netgen\LiveVotingBundle\Form\UserType;
use Netgen\LiveVotingBundle\Form\UserDataType;
use Netgen\LiveVotingBundle\Form\RegistrationUserType;

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

    private function createUserEditForm(User $entity)
    {
        $form = $this->createForm(new UserDataType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));;
        /*$form->add('Enabled', 'checkbox', array('required'=>false, 'label'=>'Enabled'));*/
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }

    private function createRegistrationEditForm(Registration $entity)
    {
        $form = $this->createForm(new RegistrationUserType(), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));;
        /*$form->add('Enabled', 'checkbox', array('required'=>false, 'label'=>'Enabled'));*/
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }



    public function EditAction()
    {

        $user_id =$this->getUser()->getId();

        $em = $this->getDoctrine()->getManager();


        $entity = $em->getRepository('LiveVotingBundle:User')->find($user_id);
        $entity2= $em->getRepository('LiveVotingBundle:Registration')->findByUser($this->getUser())[0];
        //dump($entity2);die;
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $userEditForm = $this->createUserEditForm($entity);
        $registrationEditForm = $this->createRegistrationEditForm($entity2);


        return $this->render('LiveVotingBundle:User:useredit.html.twig', array(
            'entity'      => $entity,
            'edit_user_form'   => $userEditForm->createView(),
            'edit_registration_form' => $registrationEditForm->createView()
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

        $editForm = $this->createUserEditForm($entity);
        $editRegistrationForm = $this->createRegistrationEditForm($entity2);
        $editForm->handleRequest($request);
        $editRegistrationForm->handleRequest($request);

        if ($editForm->isValid())
        {
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('user_edit'));
        }

        if ($editRegistrationForm->isValid()){
            $em->persist($entity2);
            $em->flush();
            return $this->redirect($this->generateUrl('user_edit'));
        }

        return $this->render('LiveVotingBundle:User:useredit.html.twig', array(
            'entity'      => $entity,
            'edit_user_form'   => $editForm->createView(),
            'edit_registration_form' => $editRegistrationForm->createView()
        ));
    }

    public function updateRegAction(Request $request) {

        $entity2= $em->getRepository('LiveVotingBundle:Registration')->findByUser($this->getUser())[0];

        $editRegistrationForm = $this->createRegistrationEditForm($entity2);
        $editRegistrationForm->handleRequest($request);

        if ($editRegistrationForm->isValid()){
            $entity2->setDevLevel($editRegistrationForm->get('devlevel')->getData());
            $entity2->setArrivalTime($editRegistrationForm->get('arrivaltime')->getData());
            $entity2->setDepartureTime($editRegistrationForm->get('departuretime')->getData());
            $em->flush();
            return $this->redirect($this->generateUrl('user_edit'));
        }
        return $this->render('LiveVotingBundle:User:useredit.html.twig', array(
            'entity'      => $entity,
            'edit_user_form'   => $editForm->createView(),
            'edit_registration_form' => $editRegistrationForm->createView()
        ));
    }

}