<?php

/*
 * This file is part of the Netgen LiveVoting bundle.
 *
 * https://github.com/netgen/LiveVotingBundle
 *
 */

namespace Netgen\LiveVotingBundle\Controller;

use Netgen\LiveVotingBundle\Entity\Presentation;
use Netgen\LiveVotingBundle\Exception\JoindInClientException;
use Netgen\LiveVotingBundle\Form\PresentationType;
use Netgen\LiveVotingBundle\Service\JoindInClient\JoindInClient;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\DateTime;
use Netgen\LiveVotingBundle\Service\PresentationService\Record\PresentationRecord;
use Doctrine\ORM\EntityRepository;

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
        $event = $em->getRepository('LiveVotingBundle:Event')->findOneById($event_id);
        //$entities = $this->get('live_voting.doctrine_presentation_repo')->find(array('event_id' => $event_id));
        $entities = $em->getRepository('LiveVotingBundle:Presentation')->findByEvent($event_id);

        $that = $this;
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
        ))->setCache(array( 'private' => true ));
    }

    /**
     * Creates a new Presentation entity.
     * @param $request Request
     * @param $event_id Event ID
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, $event_id)
    {
        $entity = new Presentation();
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository('LiveVotingBundle:Event')->find($event_id);
        $entity->setEvent($event);

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

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
        ))->setCache(array( 'private' => true ));
    }

    /**
     * Creates a form to create a Presentation entity.
     * @param Presentation $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Presentation $entity){

        $form = $this->createForm(new PresentationType(), $entity, array(
            'action' => $this->generateUrl('admin_presentation_create', array('event_id' => $entity->getEvent()->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create new presentation', 'attr' => array('class' => 'btn btn-large btn-primary',)));

        return $form;
        //return $form->getForm();
    }

    /**
     * Creates a form to edit a Presentation entity.
     * @param Presentation $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Presentation $entity){
        $form = $this->createForm(new PresentationType(), $entity, array(
            'action' => $this->generateUrl('admin_presentation_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('submit', 'submit', array('label' => 'Update presentation', 'attr' => array('class' => 'btn btn-large btn-primary')));

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

        $form = $this->createCreateForm($entity);
        $form->remove('votingEnabled');
        return $this->render('LiveVotingBundle:Presentation:new.html.twig', array(
            'form'   => $form->createView(),
            'event_id' => $event_id
        ))->setCache(array( 'private' => true ));
    }


    /**
     * Displays a form to edit an existing Presentation entity.
     * @param Presenatation Id $id
     * @return mixed
     */
    public function editAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entity = $entityManager->getRepository('LiveVotingBundle:Presentation')->find($id);

        $editForm = $this->createEditForm($entity);

        // Not needed in edit page
        $editForm->remove('votingEnabled');

        $presentationVotes = $entity->getVotes()->toArray();

        return $this->render('LiveVotingBundle:Presentation:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'remove_votes_enabled' => (count($presentationVotes) > 0) ? true : false
        ))->setCache(array( 'private' => true ));
    }

    /**
     * Edits an existing Presentation entity.
     */
    public function updateAction(Request $request, $id)
    {
        //$entity = $this->get('live_voting.doctrine_presentation_repo')->findOne($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entity = $entityManager->getRepository('LiveVotingBundle:Presentation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Event entity.');
        }

        $old_image = $entity->getImage();
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {


            if(empty($editForm->getData()->getImage())) {
                $entity->setImage($old_image);
            }

            $entity->upload();

            $entityManager->flush();

            $request->getSession()->getFlashBag()->add(
                'message', 'Your changes were saved.'
            );
            return $this->redirect($this->generateUrl('admin_presentation', array('event_id' => $entity->getEvent()->getId() )));
        }

        return $this->render('LiveVotingBundle:Presentation:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ))->setCache(array( 'private' => true ));
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
        if($entity->getVotingEnabled() == false)
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
        //$entity = $this->get('live_voting.doctrine_presentation_repo')->findOne($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entity = $entityManager->getRepository('LiveVotingBundle:Presentation')->find($id);

        $form = $this->createEnableDisableForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            if($form->getClickedButton()->getName()=='disable'){
                $entity->setVotingEnabled(true);
            }else{
                $entity->setVotingEnabled(false);
            }
            //$this->get('live_voting.doctrine_presentation_repo')->update($entity);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('admin_presentation', array('event_id' => $entity->getEvent()->getId() )));

    }

    public function deleteVotesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $presentationEntity = $em->getRepository('LiveVotingBundle:Presentation')->find($id);

        $entities = $presentationEntity->getVotes()->toArray();

        foreach ($entities as $entity) {
            $em->remove($entity);
        }

        $em->flush();

        $request->getSession()->getFlashBag()->add(
            'message', 'You have removed all votes for this presentation.'
        );

        return $this->redirect($this->generateUrl('admin_presentation_edit', ['id' => $id]))->setCache(['private' => true]);
    }

    /**
     * Deletes an existing Presentation entity
     * @param Presenatation Id $id
     */
    public function deleteAction($id){
        $eventId = $this->get('live_voting.doctrine_presentation_repo')->findOne($id)->getEventId();
        $entity = $this->get('live_voting.doctrine_presentation_repo')->destroy($id);
        return $this->redirect($this->generateUrl('admin_presentation', array('event_id' => $eventId )));
    }

    public function publishAction(Request $request, $event_id) {
        /**
         * @var $client JoindInClient
         */
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('LiveVotingBundle:Event')->find($event_id);
        $presentations = $em->getRepository('LiveVotingBundle:Presentation')->findBy(array('event'=>$event));
        $client = $this->get('live_voting.joind_in_client');
        foreach($presentations as $presentation) {
            /**
             * @var $presentation Presentation
             */
            if($presentation->getGlobalBrake()) continue;
            if($presentation->getJoindInId() == null)
            {
                try {
                    /**
                     * @var $publishedPresentation Presentation
                     */
                    $publishedPresentation = $client->publishPresentation($request->get("joindEvent"), $presentation);
                    $presentation . setJoindInId($publishedPresentation . getJoindInId());
                } catch (JoindInClientException $e) {
                    return new JsonResponse(array("error" => $e->getMessage()), 500);
                }
                $em->persist($presentation);
            }
        }
        $em->flush();
        return new JsonResponse(array("succesful" => true));
    }
}
