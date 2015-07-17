<?php

namespace Netgen\LiveVotingBundle\Controller;

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
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_user'));
        }

        return $this->render('LiveVotingBundle:User:new.html.twig', array(
            'user' => $user,
            'form'   => $form->createView(),
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
        $form   = $this->createCreateForm($entity);

        return $this->render('LiveVotingBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
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

        $editForm = $this->createEditForm($entity);

        return $this->render('LiveVotingBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }

    /**
    * Creates a form to edit a User entity.
    * @param User $entity The entity
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserDataType(), $entity, array(
            'action' => $this->generateUrl('admin_user_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('Enabled', 'checkbox', array('required'=>false, 'label'=>'Enabled'));
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

        if ($editForm->isValid())
        {
            $entity->setEmail($entity->getEmail());
            $em->flush();
            return $this->redirect($this->generateUrl('admin_user'));
        }

        return $this->render('LiveVotingBundle:Event:user.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }

    public function enableDisableAction(){
        // unfinished
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

    public function importCsvAction(){
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

    public function processImportCsvAction(Request $request){
      $form = $this->createFormBuilder()
        ->setAction($this->generateUrl('user_csv_process_import'))
        ->setMethod('POST')
        ->add('attachment', 'file')
        ->add('submit', 'submit', array('label' => 'Upload csv'))
        ->getForm();

      $form->handleRequest($request);

      if($form->isValid()){
        $file = $form['attachment']->getData();

        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                for($i=0;$i<count($data);$i++){
                  if(filter_var($data[$i], FILTER_VALIDATE_EMAIL)){
                    $user = $this->getDoctrine()->getRepository('LiveVotingBundle:User')->findByEmail($data[$i]);

                    if(!$user){
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
}
