<?php

/*
 * This file is part of the Netgen LiveVoting bundle.
 *
 * https://github.com/netgen/LiveVotingBundle
 *
 */

namespace Netgen\LiveVotingBundle\Controller;

use Netgen\LiveVotingBundle\Entity\Presentation;
use Netgen\LiveVotingBundle\Form\PresentationType;
use Netgen\LiveVotingBundle\Service\JoindInClient\JoindInClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Presentation controller. (admin)
 */
class PresentationAdminController extends Controller
{

    /**
     * Lists all Presentation entities.
     * @param $event_id Event ID
     */
    public function indexAction($event_id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('LiveVotingBundle:Event')->find($event_id);
        $entities = $em->getRepository('LiveVotingBundle:Presentation')->findBy(array('event'=>$event));
        /**
         * @var $client JoindInClient
         */
        $client = $this->get('live_voting.joind_in_client');
        $joindInEvents = $client->obtainUserEvents(27355, true);
        $that = $this;
        return $this->render('LiveVotingBundle:Presentation:index.html.twig', array(
            'entities' => array_map(
                function($ent) use ($that) {
                   return array($ent, $that->createEnableDisableForm($ent)->createView());
                }, $entities),
            'event' => $event,
            'joindInEvents' => $joindInEvents
        ));
    }
    /**
     * Creates a new Presentation entity.
     * @param $request Request
     * @param $event_id Event ID
     */
    public function createAction(Request $request, $event_id)
    {
        $entity = new Presentation();
        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);
        $entity->setEvent($event);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
              'message', 'You have created new presentation.'
            );

            return $this->redirect($this->generateUrl('admin_presentation', array('event_id'=>$event_id)));
        }

        return $this->render('LiveVotingBundle:Presentation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Presentation entity.
     * @param Presentation $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Presentation $entity)
    {
        $form = $this->createForm(new PresentationType(), $entity, array(
            'action' => $this->generateUrl('admin_presentation_create', array('event_id'=>$entity->getEvent()->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Presentation entity.
     * @param $event_id Event ID
     */
    public function newAction($event_id)
    {
        $entity = new Presentation();
        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);
        $entity->setEvent($event);

        $form   = $this->createCreateForm($entity);
        $form->remove('votingEnabled');
        return $this->render('LiveVotingBundle:Presentation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'event_id' => $event_id
        ));
    }


    /**
     * Displays a form to edit an existing Presentation entity.
     * @param Presenatation Id $id
     * @return mixed
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:Presentation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Presentation entity.');
        }

        $editForm = $this->createEditForm($entity);

        // Not needed in edit page
        $editForm->remove('votingEnabled');

        return $this->render('LiveVotingBundle:Presentation:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }

    /**
    * Creates a form to edit a Presentation entity.
    * @param Presentation $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Presentation $entity)
    {
        $form = $this->createForm(new PresentationType(), $entity, array(
            'action' => $this->generateUrl('admin_presentation_update', array('id' => $entity->getId())),
            'method' => 'PUT',

        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Presentation entity.
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:Presentation')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Presentation entity.');
        }
        $old_image = $entity->getImage();
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $entity->upload();
            if($entity->getImage() == null) $entity->setImage($old_image);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
              'message', 'Your changes were saved.'
            );
            return $this->redirect($this->generateUrl('admin_presentation', array('event_id' => $entity->getEvent()->getId())));
        }

        return $this->render('LiveVotingBundle:Presentation:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }

    /**
     * Creates form to create enable/disable form for presentation
     * so users can vote on it.
     * @param Presentation $entity The entity
     */
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

    /**
     * Action that enabled and disables presentation.
     * @param Request $param
     * @param Presenatation Id $id
     */
    public function enableDisableAction(Request $request, $id){
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:Presentation')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Presentation entity.');
        }

        $form = $this->createEnableDisableForm($entity, 'enabled', array());
        $form->handleRequest($request);
        if ($form->isValid()) {
            if($form->getClickedButton()->getName()=='disable'){
                $entity->setVotingEnabled(true);
            }else{
                $entity->setVotingEnabled(false);
            }
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_presentation', array('event_id' => $entity->getEvent()->getId())));

    }

    /**
     * Deletes an existing Presentation entity
     * @param Presenatation Id $id
     */
    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:Presentation')->find($id);
        $eventId = $entity->getEvent()->getId();

        if (!$entity) {
            throw $this->createNotFoundException('Presentation is already removed.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_presentation', array('event_id' => $eventId )));
    }

}
