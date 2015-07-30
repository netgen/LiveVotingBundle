<?php


namespace Netgen\LiveVotingBundle\Controller;

use Proxies\__CG__\Netgen\LiveVotingBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Netgen\LiveVotingBundle\Entity\Presentation;
use Netgen\LiveVotingBundle\Form\PresentationUserType;
use Netgen\LiveVotingBundle\Service\PresentationService\Record\PresentationRecord;


class PresentationUserController extends Controller
{
    public function indexAction()
    {
        $user_id = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LiveVotingBundle:User')->find($user_id);

        $entities = $this->get('live_voting.doctrine_presentation_repo')->find(array('user'=>$user));

        $that = $this;
        
        return $this->render('LiveVotingBundle:Presentation:user.html.twig', array(
            'entities' => array_map(
                function ($ent) use ($that) {
                    return array($ent, $that->createEnableDisableForm($ent)->createView());
                }, $entities),
            'user' => $user
        ));

    }

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

    public function editAction($id)
    {
        $entity = $this->get('live_voting.doctrine_presentation_repo')->findOne($id);

        $editForm = $this->createEditForm($entity);

        $event = $this->getDoctrine()->getManager()->getRepository('LiveVotingBundle:Event')->findOneById($entity->getEventId());

        // Not needed in edit page
        $editForm->remove('votingEnabled');

        return $this->render('LiveVotingBundle:Presentation:useredit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'event'       => $event
        ));
    }

    /**
     * Creates a form to edit a Presentation entity.
     * @param Presentation $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PresentationRecord $entity)
    {
        $form = $this->createForm(new PresentationUserType(), $entity, array(
            'action' => $this->generateUrl('user_presentation_update', array('id' => $entity->getId())),
            'method' => 'PUT',

        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    public function updateAction(Request $request, $id)
    {
        $entity = $this->get('live_voting.doctrine_presentation_repo')->findOne($id);


        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->get('live_voting.doctrine_presentation_repo')->update($entity);

            $request->getSession()->getFlashBag()->add(
              'message', 'Your changes were saved.'
            );
          return $this->redirect($this->generateUrl('user_presentation_edit', array('id' => $entity->getId())));
        }

        return $this->render('LiveVotingBundle:Presentation:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }

    /**
     * Action that enabled and disables presentation.
     * @param Request $param
     * @param Presenatation Id $id
     */

}
