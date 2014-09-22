<?php

namespace Netgen\LiveVotingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Netgen\LiveVotingBundle\Entity\Event;
use Netgen\LiveVotingBundle\Entity\Question;
use Netgen\LiveVotingBundle\Form\EventType;

/**
 * Event controller.
 *
 */
class EventAdminController extends Controller
{

    /**
     * Lists all Event entities.
     *
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
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Event();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
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
     *
     * @param Event $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('admin_event_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Event entity.
     *
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
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:Event')->find($id);

        if (!$entity) {
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
    *
    * @param Event $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Event $entity)
    {
        $form = $this->createForm(new EventType(), $entity, array(
            'action' => $this->generateUrl('admin_event_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('allowViewingResults', 'checkbox', array('required'=>false, 'label'=>'Results available for public'));
        $form->add('numberOfSeconds', 'number', array('mapped'=> false, 'required'=>false, 'label'=>'Seconds until event ends.'));
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }
    /**
     * Edits an existing Event entity.
     *
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

        if ($editForm->isValid()) {
                /**
                 * find all questions of the event and set votingEnabled value to current value of event
                 */
            $questions = $em->getRepository('LiveVotingBundle:Question')->FindBy(array('event' => $entity));
            $newValue = true;
            switch($editForm->get('stateName')){
                case "PRE":
                    $newValue = false;
                    break;
                case "ACTIVE":
                    $newValue = true;
                    break;
                case "POST":
                    $newValue = false;
                    break;
            }
            foreach ($questions as $question) {
                $question->setVotingEnabled($newValue);           
            }

                //rest of query
            $entity->setStateValue(time() + intval($editForm->get('numberOfSeconds')->getData()));
            $entity->upload();
            $em->flush();
            return $this->redirect($this->generateUrl('admin_event'));
        }

        return $this->render('LiveVotingBundle:Event:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }

    public function enableDisableAction(){
        // DELETE ME (MEJBI)
    }

    // TODO: Implement later if neede1d
    private function createEnableDisableForm(Event $event){
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_event_enabledisable', array('event_id' => $event->getId())))
            ->setMethod('PUT')
            ->add('enabledisable', 'submit')
            ->getForm()
        ;
    }

    public function deleteAction($id){
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:Event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Event is already removed.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_event'));
    }
}
