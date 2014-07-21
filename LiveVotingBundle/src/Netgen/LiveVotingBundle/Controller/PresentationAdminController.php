<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Netgen\LiveVotingBundle\Entity\Presentation;
use Netgen\LiveVotingBundle\Form\PresentationType;

/**
 * Presentation controller.
 *
 */
class PresentationAdminController extends Controller
{

    /**
     * Lists all Presentation entities.
     *
     */
    public function indexAction($event_id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('LiveVotingBundle:Event')->find($event_id);
        $entities = $em->getRepository('LiveVotingBundle:Presentation')->findBy(array('event'=>$event));
        return $this->render('LiveVotingBundle:Presentation:index.html.twig', array(
            'entities' => array_map(
                function($ent){
                   return array($ent, $this->createEnableDisableForm($ent)->createView());
                }, $entities),
            'event_id' => $event_id
        ));
    }
    /**
     * Creates a new Presentation entity.
     *
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
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_presentation', array('event_id'=>$event_id)));
        }

        return $this->render('LiveVotingBundle:Presentation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Presentation entity.
     *
     * @param Presentation $entity The entity
     *
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
     *
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
     * Finds and displays a Presentation entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:Presentation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Presentation entity.');
        }


        return $this->render('LiveVotingBundle:Presentation:show.html.twig', array(
            'entity'      => $entity
        ));
    }

    /**
     * Displays a form to edit an existing Presentation entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:Presentation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Presentation entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->remove('votingEnabled');
        return $this->render('LiveVotingBundle:Presentation:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }

    /**
    * Creates a form to edit a Presentation entity.
    *
    * @param Presentation $entity The entity
    *
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
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:Presentation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Presentation entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_presentation', array('event_id' => $entity->getEvent()->getId())));
        }

        return $this->render('LiveVotingBundle:Presentation:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }


    private function createEnableDisableForm(Presentation $entity)
    {
        $form = $this->createFormBuilder();
        $form->setMethod('PUT');
        $form->setAction($this->generateUrl('admin_presentation_vote_enable', array('id'=>$entity->getId())));
        if($entity->getVotingEnabled()==False)
            $form->add('disable', 'submit',  array('label'=>'Disabled', 'attr'=>array('class'=>'btn btn-danger')));
        else
            $form->add('enable', 'submit',  array('label'=>'Enabled', 'attr'=>array('class'=>'btn btn-success')));

        return $form->getForm();
    }


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

}
