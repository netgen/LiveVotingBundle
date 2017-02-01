<?php

namespace Netgen\LiveVotingBundle\Controller;

use Netgen\LiveVotingBundle\Entity\Event;
use Netgen\LiveVotingBundle\Entity\UserEventAssociation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Netgen\LiveVotingBundle\Entity\User;
use Netgen\LiveVotingBundle\Form\UserType;
use Netgen\LiveVotingBundle\Form\UserDataType;


/**
 * Event controller.
 */
class UserAdminController extends Controller
{

    /**
     * Lists all User entities.
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = array();
        $masterEvents = $this->extractRootEvents();

        $form = $this->createForm(
            'live_voting_bundle_users_by_event_type',
            array(
                'accessible_events' => $masterEvents
            ),
            array(
                'action' => $this->generateUrl('admin_user'),
                'method' => 'POST',
            ));

        $form->handleRequest($request);

        $params = array();

        if($form->isSubmitted()) {
            $formData = $form->getData();

            $params['event'] = $formData['event'];
        }

        $userEventAssociations = $em->getRepository('LiveVotingBundle:UserEventAssociation')->findBy(
            $params
        );

        $userIds = array_map(
            function(UserEventAssociation $userEventAssociation)
            {
                return $userEventAssociation->getUser()->getId();
            },
            $userEventAssociations
        );

        $allEntities = $em->getRepository('LiveVotingBundle:User')->findBy(array(), array('email' => 'ASC'));

        $entities = array_filter(
            $allEntities,
            function(User $entity) use ($userIds) {
                if ( in_array($entity->getId(), $userIds) )
                {
                    return $entity;
                }
            }
        );
        
        return $this->render('LiveVotingBundle:User:index.html.twig', array(
            'entities' => $entities,
            'count' => count($entities),
            'form' => $form->createView()
        ))->setCache(array( 'private' => true ));
    }


    /**
     * Creates a new User entity.
     * @param Request $request
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createCreateForm($user);
        $form->handleRequest($request);
        $eventData = $form->get('event')->getData();

        $idd = uniqid(rand(), true);
        $user->setId($idd);
        $user->setUsername($idd);
        $user->setPassword('1');

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            if ($em->getRepository('LiveVotingBundle:User')->findOneByEmail($user->getEmail())) {
                $request->getSession()->getFlashBag()->add(
                    'error', 'That user already exists.'
                );

                return $this->redirect($this->generateUrl('admin_user_new'));
            }

            $em->persist($user);

            $userEventAssociation = new UserEventAssociation();
            $userEventAssociation->setUser($user);
            $userEventAssociation->setEvent($eventData);

            $em->persist($userEventAssociation);

            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'message', 'You have added new user.'
            );

            return $this->redirect($this->generateUrl('admin_user'));
        }

        return $this->render('LiveVotingBundle:User:new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ))->setCache(array( 'private' => true ));
    }

    /**
     * Creates a form to create a User entity.
     * @param User $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $rootEvents = $this->extractRootEvents();

        $form = $this->createForm(
            new UserType(),
            $entity,
            array(
            'action' => $this->generateUrl('admin_user_create'),
            'method' => 'POST',
        ));

        $form->add(
            'event',
            'entity',
            array(
                'class' => 'Netgen\LiveVotingBundle\Entity\Event',
                'label' => 'Assign To Event',
                'required' => true,
                'mapped' => false,
                'choices' => $rootEvents
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     */
    public function newAction()
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);

        return $this->render('LiveVotingBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
        ))->setCache(array( 'private' => true ));
    }


    /**
     * Displays a form to edit an existing User entity.
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $eventAssociations = $entity->getEventAssociations();

        $associatedEvents = array();

        if ($eventAssociations->count() > 0) {
            /** @var UserEventAssociation $userEventAssociation */
            foreach ($eventAssociations as $eventAssociation) {
                $associatedEvents[] = $eventAssociation->getEvent();
            }
        }

        $editForm = $this->createEditForm($entity, count($associatedEvents) > 0 ? $associatedEvents : null);

        $userEventAssociationAddForm = $this->userEventAssociationAddForm($id);

        return $this->render('LiveVotingBundle:User:edit.html.twig', array(
            'user_id' => $id,
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'associated_events' => count($associatedEvents) > 0 ? $associatedEvents : null,
            'user_event_association_form' => $userEventAssociationAddForm->createView()
        ))->setCache(array( 'private' => true ));
    }

    /**
     * Creates a form to edit a User entity.
     * @param User $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity, $associatedEvents = null)
    {
        $form = $this->createForm(new UserDataType(), $entity, array(
            'action' => $this->generateUrl('admin_user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('Enabled', 'checkbox', array('required' => false, 'label' => 'Enabled'));
        $form->add('submit', 'submit', array('label' => 'Update'));
        return $form;
    }

    /**
     * Edits an existing User entity.
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiveVotingBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setEmail($entity->getEmail());
            $em->flush();
            $request->getSession()->getFlashBag()->add(
                'message', 'Your changes were saved.'
            );

            return $this->redirect($this->generateUrl('admin_user'));
        }

        return $this->render('LiveVotingBundle:Event:user.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView()
        ))->setCache(array( 'private' => true ));
    }

    public function enableDisableAction()
    {
        // unfinished
    }

    // TODO: Implement later if neede1d
    private function createEnableDisableForm(Event $event)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_event_enabledisable', array('event_id' => $event->getId())))
            ->setMethod('PUT')
            ->add('enabledisable', 'submit')
            ->getForm();
    }

    public function importCsvAction()
    {
        $rootEvents = $this->extractRootEvents();

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('user_csv_process_import'))
            ->setMethod('POST')
            ->add('attachment', 'file')
            ->add('event', 'entity', array(
                'class' => 'Netgen\LiveVotingBundle\Entity\Event',
                'label' => 'Assign To Event',
                'required' => true,
                'choices' => $rootEvents
            ))
            ->add('submit', 'submit', array('label' => 'Upload csv'))
            ->getForm();
        return $this->render(
            'LiveVotingBundle:User:importcsv.html.twig',
            array(
                'form' => $form->createView()
            )
        )->setCache(array( 'private' => true ));
    }

    public function processImportCsvAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('user_csv_process_import'))
            ->setMethod('POST')
            ->add('attachment', 'file')
            ->add('event', 'entity', array(
                'class' => 'Netgen\LiveVotingBundle\Entity\Event',
                'label' => 'Assign To Event',
                'required' => true
            ))
            ->add('submit', 'submit', array('label' => 'Upload csv'))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isValid()) {
            $file = $form['attachment']->getData();

            $event = $form['event']->getData();

            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                    for ($i = 0; $i < count($data); $i++) {
                        if (filter_var($data[$i], FILTER_VALIDATE_EMAIL)) {
                            /** @var User $user */
                            $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->findByEmail($data[$i]);

                            if (!$user) {
                                $newUser = new User();
                                $newUser->setEmail($data[$i]);
                                $idd = uniqid(rand(), true);
                                $newUser->setId($idd);
                                $newUser->setUsername($idd);
                                $newUser->setPassword('1');
                                $newUser->setEnabled(true);

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($newUser);

                                $userEventAssociation = new UserEventAssociation();

                                $userEventAssociation->setUser($newUser);
                                $userEventAssociation->setEvent($event);

                                $em->persist($userEventAssociation);

                                $em->flush();
                            }
                            else
                            {
                                $userEventAssociations = $user->getEventAssociations();

                                if ( $userEventAssociations->count() == 0 )
                                {
                                    $userEventAssociation = new UserEventAssociation();

                                    $userEventAssociation->setUser($user);
                                    $userEventAssociation->setEvent($event);

                                    $em = $this->getDoctrine()->getManager();
                                    $em->persist($userEventAssociation);
                                    $em->flush();
                                }
                            }
                        }
                    }
                }
                fclose($handle);
                $request->getSession()->getFlashBag()->add(
                    'message', 'You have added new user(s) from csv file.'
                );
            }
            return $this->redirect($this->generateUrl('admin_user'));
        }

        return $this->render(
            'LiveVotingBundle:User:importcsv.html.twig',
            array(
                'form' => $form->createView()
            )
        )->setCache(array( 'private' => true ));
    }

    public function loginEmailAction(Request $request, $typeOf, $eventId)
    {
        $users = array();

        $event = $this->getDoctrine()->getRepository('LiveVotingBundle:Event')->find($eventId);

        $userAssociations = $event->getUserAssociations();

        if ( $userAssociations->count() > 0 )
        {
            $emailSubject = $event->getEmailSubject();
            $emailText = $event->getEmailText();

            foreach ($userAssociations as $userAssociation)
            {
                $users[] = $userAssociation->getUser();
            }

            foreach ($users as $user) {
                $user_email = $user->getEmail();
                $emailHash = md5($this->container->getParameter('email_hash_prefix') . $user_email);
                if ($typeOf === '0') {
                    $message = \Swift_Message::newInstance()
                        ->setSubject($emailSubject !== '' ? $emailSubject : 'CSSF & SSD 2016 workshops voting')
                        ->setFrom(array('info@salsa-adria.hr' => 'Salsa Adria Productions'))
                        ->setTo($user_email)
                        ->setBody(
                            $this->renderView(
                                'LiveVotingBundle:Email:login.html.twig',
                                array(
                                    'emailHash' => $emailHash,
                                    'emailText' => $emailText
                                )
                            ),
                            'text/html'
                        );
                } else if ($typeOf === '1') {
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Questionnaire')
                        ->setFrom(array('info@salsa-adria.hr' => 'Salsa Adria Productions'))
                        ->setTo($user_email)
                        ->setBody(
                            $this->renderView(
                                'LiveVotingBundle:Email:questions.html.twig',
                                array('emailHash' => $emailHash)
                            ),
                            'text/html'
                        );
                }

                $this->get('mailer')->send($message);
            }

            if ($typeOf === '0') {
                $request->getSession()->getFlashBag()->add(
                    'message', 'Activations have been sent to all users.'
                );
            } else if ($typeOf === '1') {
                $request->getSession()->getFlashBag()->add(
                    'message', 'Questionnaires have been sent to all users.'
                );
            }
            return $this->redirect($this->generateUrl('admin_event'));
        }
        else
        {
            $request->getSession()->getFlashBag()->add(
                'message', 'No users found for this event. Please assign users to the event in order to send out the proper e-mails.'
            );

            return $this->redirect($this->generateUrl('admin_event'));
        }


    }

    public function oneUserLoginEmailAction(Request $request, $id, $typeOf)
    {

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('LiveVotingBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $accessibleEventAssociations = $user->getEventAssociations();

        $form = null;
        $message = null;

        if ($accessibleEventAssociations->count() > 0)
        {
            $accessibleEvents = array();
            foreach ($accessibleEventAssociations as $accessibleEventAssociation)
            {
                $accessibleEvents[] = $accessibleEventAssociation->getEvent();
            }

            $form = $this->createForm(
                'live_voting_user_send_one_login_email',
                array('user_id' => $id, 'accessible_events' => $accessibleEvents),
                array(
                    'action' => $this->generateUrl('send_one_email_login', array('id' => $id, 'typeOf' => $typeOf)),
                    'method' => 'POST'
                )
            );

            $form->add('submit', 'submit', array('label' => 'Send'));

            $form->handleRequest($request);

            if ($form->isSubmitted())
            {
                $formData = $form->getData();

                /** @var Event $selectedEvent */
                $selectedEvent = $formData['event'];

                $emailSubject = $selectedEvent->getEmailSubject();
                $emailText = $selectedEvent->getEmailText();

                $user_email = $user->getEmail();
                $emailHash = md5($this->container->getParameter('email_hash_prefix') . $user_email);
                if ($typeOf === '0') {
                    $message = \Swift_Message::newInstance()
                        ->setSubject($emailSubject !== '' ? $emailSubject : 'CSSF & SSD 2016 workshops voting')
                        ->setFrom(array('info@salsa-adria.hr' => 'Salsa Adria Productions'))
                        ->setTo($user_email)
                        ->setBody(
                            $this->renderView(
                                'LiveVotingBundle:Email:login.html.twig',
                                array(
                                    'emailHash' => $emailHash,
                                    'emailText' => $emailText
                                )
                            ),
                            'text/html'
                        );
                } else if ($typeOf === '1') {
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Questionnaire')
                        ->setFrom(array('info@salsa-adria.hr' => 'Salsa Adria Productions'))
                        ->setTo($user_email)
                        ->setBody(
                            $this->renderView(
                                'LiveVotingBundle:Email:questions.html.twig',
                                array('emailHash' => $emailHash)
                            ),
                            'text/html'
                        );
                }

                $this->get('mailer')->send($message);
                if ($typeOf === '0') {
                    $request->getSession()->getFlashBag()->add(
                        'message', 'Activation has been sent to ' . $user_email
                    );
                } else if ($typeOf === '1') {
                    $request->getSession()->getFlashBag()->add(
                        'message', 'Questionnaire has been sent to ' . $user_email
                    );
                }

                return $this->redirect($this->generateUrl('admin_user'));
            }
        }
        else
        {
            $message = 'No events are available to this user. Please assign one to send an email.';
        }

        return $this->render('LiveVotingBundle:User:one_login_send.html.twig', array(
            'send_form' => $form ? $form->createView() : $form,
            'message' => $message,
            'type_of' => $typeOf
        ))->setCache(array( 'private' => true ));
    }

    public function userEventAssociationAddForm($userId)
    {
        $rootEvents = $this->extractRootEvents();

        $form = $this->createForm(
            'live_voting_user_event_add_form',
            array(
                'user_id' => $userId,
                'choices' => $rootEvents
            ),
            array(
                'action' => $this->generateUrl('admin_user_event_association_add', array('userId' => $userId)),
                'method' => 'POST'
            )
        );
        $form->add('submit', 'submit', array('label' => 'Add'));
        return $form;
    }

    public function userEventAssociationAddAction(Request $request, $userId)
    {
        $userEventAssociationAddForm = $this->userEventAssociationAddForm($userId);

        $userEventAssociationAddForm->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();

        $userEventAssociationEntityRepository = $this->getDoctrine()->getRepository('LiveVotingBundle:UserEventAssociation');

        $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->find($userId);

        $event = $userEventAssociationAddForm->get('event')->getData();

        $userEventAssociation = $userEventAssociationEntityRepository->findBy(array(
            'user' => $userId,
            'event' => $event->getId()
        ));

        if (count($userEventAssociation) == 0)
        {
            $userEventAssociation = new UserEventAssociation();

            $userEventAssociation->setEvent($event);
            $userEventAssociation->setUser($user);

            $entityManager->persist($userEventAssociation);
            $entityManager->flush($userEventAssociation);
        }

        $url = $this->generateUrl('admin_user_edit', array('id' => $userId));

        $request->getSession()->getFlashBag()->add(
            'message', 'You have assigned the user to a new event.'
        );

        return $this->redirect($url);
    }

    public function userEventAssociationRemoveAction(Request $request, $userId, $eventId)
    {
        $em = $this->getDoctrine()->getManager();

        $userEventAssociation = $em->getRepository('LiveVotingBundle:UserEventAssociation')->findOneBy(
            array(
                'user' => $userId,
                'event' => $eventId
            )
        );

        $em->remove($userEventAssociation);
        $em->flush();

        $request->getSession()->getFlashBag()->add(
            'message', 'You have removed an event from this user.'
        );

        return $this->redirect($this->generateUrl('admin_user_edit', array('id' => $userId)));
    }

    public function deleteAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $entity = $entityManager->getRepository('LiveVotingBundle:User')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('User is already removed.');
        }

        $entityManager->remove($entity);
        $entityManager->flush();

        $request->getSession()->getFlashBag()->add(
            'message', 'You have removed a user.'
        );

        return $this->redirect($this->generateUrl('admin_user'));
    }

    public function extractRootEvents()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityRepository = $entityManager->getRepository('LiveVotingBundle:Event');

        $events = $entityRepository->findBy(array(), array('id' => 'DESC'));

        $rootEvents = array();

        foreach($events as $event)
        {
            if ($event->getEvents()->count() > 0)
            {
                $rootEvents[] = $event;
            }
        }

        return $rootEvents;
    }
}
