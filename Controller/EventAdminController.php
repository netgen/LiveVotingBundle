<?php

/*
 * This file is part of the Netgen LiveVoting bundle.
 *
 * https://github.com/netgen/LiveVotingBundle
 *
 */

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Netgen\LiveVotingBundle\Entity\Event;
use Netgen\LiveVotingBundle\Entity\Question;
use Netgen\LiveVotingBundle\Form\EventType;

/**
 * Event controller. (admin)
 */
class EventAdminController extends Controller
{

    /**
     * Lists all Event entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LiveVotingBundle:Event')->findAll();

        return $this->render('LiveVotingBundle:Event:index.html.twig', array(
            'entities' => $entities,
        ));
    }


    /**
     * Creates a new Event entity.
     * @param $request Request
     */
    public function createAction(Request $request)
    {
        $entity = new Event();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $entity->upload();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_event'));
        }

        return $this->render('LiveVotingBundle:Event:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Event entity.
     * @param Event $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('admin_event_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create new event', 'attr' => array('class' => 'btn btn-large btn-primary',)));

        return $form;
    }

    /**
     * Displays a form to create a new Event entity.
     */
    public function newAction()
    {
        $entity = new Event();
        $form   = $this->createCreateForm($entity);

        return $this->render('LiveVotingBundle:Event:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing Event entity.
     * @param $id Event ID
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:Event')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('LiveVotingBundle:Event:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }

    /**
    * Creates a form to edit a Event entity.
    * @param Event $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Event $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $event_id = $entity->getId();
        $questions = $em->getRepository('LiveVotingBundle:Question')->findBy(array('event' => $event_id));

        if($questions)
        {
            $questionStatus = $questions[0]->getVotingEnabled();
        }

        else
        {
            $questionStatus = false;
        }

        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('admin_event_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('allowViewingResults', 'checkbox', array('required' => false, 'label' => 'Results available for public: '));
        $form->add('questionStatus', 'checkbox', array('mapped'=> false, 'required'=>false, 'label'=>'Questions enabled for this event: ', 'attr' => array('checked' => $questionStatus)));
        $form->add('numberOfSeconds', 'number', array('mapped'=> false, 'required'=>false, 'label'=>'Seconds until event ends: ', 'attr' => array('class'=> 'form-control')));
        $form->add('submit', 'submit', array('label' => 'Update event', 'attr' => array('class' => 'btn btn-large btn-primary')));
        return $form;
    }

    /**
     * Edits an existing Event entity.
     * @param $request Request
     * @param $id Event ID
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid())
        {

            //find all questions of the event and set votingEnabled value to current value of event
            $status = $editForm->get('questionStatus')->getData();
            $questions = $em->getRepository('LiveVotingBundle:Question')->findBy(array('event' => $entity));
            $newValue = 1;

            switch($status)
            {
                case 1:
                    $newValue = true;
                    break;
                case 0:
                    $newValue = false;
                    break;
            }

            foreach ($questions as $question)
            {
                $question->setVotingEnabled($newValue);
            }

            $entity->setStateValue(time() + intval($editForm->get('numberOfSeconds')->getData()));
            $entity->upload();
            $em->flush();

            $request->getSession()->getFlashBag()->add(
              'message', 'Your changes were saved.'
            );
            
            return $this->redirect($this->generateUrl('admin_event'));
        }

        return $this->render('LiveVotingBundle:Event:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }

    /**
     * Deletes an existing Event entity.
     * @param $id Event ID
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:Event')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Event is already removed.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_event'));
    }
}
