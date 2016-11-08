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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LiveVotingBundle:User')->findAll();

        return $this->render('LiveVotingBundle:User:index.html.twig', array(
            'entities' => $entities,
        ));
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
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'message', 'You have added new user.'
            );

            return $this->redirect($this->generateUrl('admin_user'));
        }

        return $this->render('LiveVotingBundle:User:new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a User entity.
     * @param User $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('admin_user_create'),
            'method' => 'POST',
        ));

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
        ));
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

        $userEventAssociations = $em->getRepository('LiveVotingBundle:UserEventAssociation')->findBy(array('userId' => $id));

        $associatedEventIds = array();

        if (count($userEventAssociations) > 0) {
            foreach ($userEventAssociations as $userEventAssociation) {
                $associatedEventIds[] = $userEventAssociation->getEventId();
            }
        }

        $associatedEvents = $em->getRepository('LiveVotingBundle:Event')->findBy(array('id' => $associatedEventIds));

        $editForm = $this->createEditForm($entity, count($associatedEventIds) > 0 ? $associatedEvents : null);

        $userEventAssociationAddForm = $this->userEventAssociationAddForm($id);

        return $this->render('LiveVotingBundle:User:edit.html.twig', array(
            'user_id' => $id,
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'associated_events' => count($associatedEventIds) > 0 ? $associatedEvents : null,
            'user_event_association_form' => $userEventAssociationAddForm->createView()
        ));
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
        ));
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
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('user_csv_process_import'))
            ->setMethod('POST')
            ->add('attachment', 'file')
            ->add('submit', 'submit', array('label' => 'Upload csv'))
            ->getForm();
        return $this->render(
            'LiveVotingBundle:User:importcsv.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    public function processImportCsvAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('user_csv_process_import'))
            ->setMethod('POST')
            ->add('attachment', 'file')
            ->add('submit', 'submit', array('label' => 'Upload csv'))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isValid()) {
            $file = $form['attachment']->getData();

            if (($handle = fopen($file, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                    for ($i = 0; $i < count($data); $i++) {
                        if (filter_var($data[$i], FILTER_VALIDATE_EMAIL)) {
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
                                $em->flush();
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
        );
    }

    public function loginEmailAction(Request $request, $typeOf)
    {
        $users = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->findAll();
        foreach ($users as $user) {
            $user_email = $user->getEmail();
            $emailHash = md5($this->container->getParameter('email_hash_prefix') . $user_email);
            if ($typeOf === '0') {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Web Summer Camp workshop voting')
                    ->setFrom(array('info@netgen.hr' => 'Web Summer Camp'))
                    ->setTo($user_email)
                    ->setBody(
                        $this->renderView(
                            'LiveVotingBundle:Email:login.html.twig',
                            array('emailHash' => $emailHash)
                        ),
                        'text/html'
                    );
            } else if ($typeOf === '1') {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Questionnaire')
                    ->setFrom(array('info@netgen.hr' => 'Web Summer Camp'))
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
        return $this->redirect($this->generateUrl('admin_user'));
    }

    public function oneUserLoginEmailAction(Request $request, $id, $typeOf)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('LiveVotingBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $user_email = $user->getEmail();
        $emailHash = md5($this->container->getParameter('email_hash_prefix') . $user_email);
        if ($typeOf === '0') {
            $message = \Swift_Message::newInstance()
                ->setSubject('Web Summer Camp workshop voting')
                ->setFrom(array('info@netgen.hr' => 'Web Summer Camp'))
                ->setTo($user_email)
                ->setBody(
                    $this->renderView(
                        'LiveVotingBundle:Email:login.html.twig',
                        array('emailHash' => $emailHash)
                    ),
                    'text/html'
                );
        } else if ($typeOf === '1') {
            $message = \Swift_Message::newInstance()
                ->setSubject('Questionnaire')
                ->setFrom(array('info@netgen.hr' => 'Web Summer Camp'))
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

    public function userEventAssociationAddForm($userId)
    {
        $form = $this->createForm(
            'live_voting_user_event_add_form',
            array(
                'user_id' => $userId
            ),
            array(
                'action' => $this->generateUrl('admin_user_event_association_add', array('userId' => $userId)),
                'method' => 'POST',
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

        $event = $userEventAssociationAddForm->get('event')->getData();

        $userEventAssociation = $userEventAssociationEntityRepository->findBy(array(
            'userId' => $userId,
            'eventId' => $event->getId()
        ));

        if (count($userEventAssociation) == 0)
        {
            $userEventAssociation = new UserEventAssociation();

            $userEventAssociation->setEventId($event->getId());
            $userEventAssociation->setUserId($userId);

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

        $userEventAssociation = $em->getRepository('LiveVotingBundle:UserEventAssociation')->findOneBy(array('userId' => $userId, 'eventId' => $eventId));

        $em->remove($userEventAssociation);
        $em->flush();

        $request->getSession()->getFlashBag()->add(
            'message', 'You have removed an event from this user.'
        );

        return $this->redirect($this->generateUrl('admin_user_edit', array('id' => $userId)));
    }
}
