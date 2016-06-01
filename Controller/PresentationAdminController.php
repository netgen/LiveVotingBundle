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
        $entities = $this->get('live_voting.doctrine_presentation_repo')->find(array('event_id' => $event_id));
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, $event_id)
    {
        $entity = new PresentationRecord();
        $entity->setEventId($event_id);
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->presenterName = $form->getData()['presenterName'];
            $entity->presenterSurname = $form->getData()['presenterSurname'];
            $entity->setUserId($form->getData()['user_id']->getId());
            $this->get('live_voting.doctrine_presentation_repo')->save($entity);

            if($form->getData()['presentationRecord']->getImageUrl())
              $form->getData()['presentationRecord']->getImageUrl()->move($this->get('live_voting.doctrine_presentation_repo')->getImageUploadRootDir(), $form->getData()['presentationRecord']->getImageUrl()->getClientOriginalName());


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
     * @param PresentationRecord $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PresentationRecord $entity){
      $form = $this->createFormBuilder()
            ->add('presentationRecord', new PresentationType(), array('data'=>$entity))
            ->add('presenterName')
            ->add('presenterSurname', null,array('required' => false))
            ->add('user_id', 'entity', array(
              'attr' => array('class' => 'form-control'),
              'label' => "Presenter",
              'query_builder' => function(EntityRepository $repository) {
                  return $repository->createQueryBuilder('u')->orderBy('u.email', 'ASC');
              },
              'class' => 'LiveVotingBundle:User',
              'property' => 'email',
              'required'    => false,
              'empty_value' => '(Select user)',
              'empty_data' => null))
            ->add('submit', 'submit', array('label' => 'Create'))
            ->setMethod('POST')
            ->setAction($this->generateUrl('admin_presentation_create', array('event_id'=>$entity->getEventId())));
      return $form->getForm();
    }

    /**
     * Creates a form to edit a Presentation entity.
     * @param Presentation $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */

    private function createEditForm(PresentationRecord $entity){
      $em = $this->getDoctrine()->getManager();

      $form = $this->createFormBuilder()
            ->add('presentationRecord', new PresentationType(), array('data'=>$entity))
            ->add('presenterName', 'text', array('data' => $entity->presenterName))
            ->add('presenterSurname', 'text', array('data' => $entity->presenterSurname, 'required' => false))
            ->add('user', 'entity', array(
              'class' => 'LiveVotingBundle:User',
              'property' => 'email',
              'data' => $em->getReference("LiveVotingBundle:User", $entity->getUserId())
            ))
            ->add('submit', 'submit', array('label' => 'Edit'))
            ->setMethod('POST')
            ->setAction($this->generateUrl('admin_presentation_update', array('id' => $entity->getId())));
      return $form->getForm();
    }

    /**
     * Displays a form to create a new Presentation entity.
     * @param $event_id Event ID
     */
    public function newAction($event_id)
    {
        $entity = new PresentationRecord();
        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($event_id);
        $entity->setEventId($event_id);

        $form = $this->createCreateForm($entity);
        $form->remove('votingEnabled');
        return $this->render('LiveVotingBundle:Presentation:new.html.twig', array(
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
        $entity = $this->get('live_voting.doctrine_presentation_repo')->findOne($id);

        $editForm = $this->createEditForm($entity);

        // Not needed in edit page
        $editForm->remove('votingEnabled');

        return $this->render('LiveVotingBundle:Presentation:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView()
        ));
    }

    /**
     * Edits an existing Presentation entity.
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->get('live_voting.doctrine_presentation_repo')->findOne($id);

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $entity->presenterName = $editForm->getData()['presenterName'];
            $entity->presenterSurname = $editForm->getData()['presenterSurname'];
            $entity->setUserId($editForm->getData()['user']->getId());

            $this->get('live_voting.doctrine_presentation_repo')->update($entity);

            if(!empty($editForm->getData()['presentationRecord']->getImageUrl())) {
                $editForm->getData()['presentationRecord']->getImageUrl()->move($this->get('live_voting.doctrine_presentation_repo')->getImageUploadRootDir(), $editForm->getData()['presentationRecord']->getImageUrl()->getClientOriginalName());
            } else {

            }

            $request->getSession()->getFlashBag()->add(
              'message', 'Your changes were saved.'
            );
            return $this->redirect($this->generateUrl('admin_presentation', array('event_id' => $entity->getEventId())));
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
    public function createEnableDisableForm(PresentationRecord $entity){
        $form = $this->createFormBuilder();
        $form->setMethod('PUT');
        $form->setAction($this->generateUrl('admin_presentation_vote_enable', array('id'=>$entity->getId())));
        if($entity->isVotingEnabled()==False)
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
        $entity = $this->get('live_voting.doctrine_presentation_repo')->findOne($id);

        $form = $this->createEnableDisableForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            if($form->getClickedButton()->getName()=='disable'){
                $entity->setVotingEnabled(true);
            }else{
                $entity->setVotingEnabled(false);
            }
            $this->get('live_voting.doctrine_presentation_repo')->update($entity);
        }

        return $this->redirect($this->generateUrl('admin_presentation', array('event_id' => $entity->getEventId())));

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
