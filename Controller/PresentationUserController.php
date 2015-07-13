<?php


namespace Netgen\LiveVotingBundle\Controller;

use Proxies\__CG__\Netgen\LiveVotingBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Netgen\LiveVotingBundle\Entity\Presentation;
use Netgen\LiveVotingBundle\Form\PresentationUserType;


class PresentationUserController extends Controller
{
    public function indexAction()
    {
        $user_id = $this->getUser()->getId();
        //dump($user_id);die;

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('LiveVotingBundle:User')->find($user_id);
        $entities = $em->getRepository('LiveVotingBundle:Presentation')->findByUser($this->getUser());
        //dump($entities);die;
        $that = $this;


        return $this->render('LiveVotingBundle:Presentation:user.html.twig', array(
            'entities' => array_map(
                function ($ent) use ($that) {
                    return array($ent, $that->createEnableDisableForm($ent)->createView());
                }, $entities),
            'user' => $user
        ));

    }
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

        return $this->render('LiveVotingBundle:Presentation:useredit.html.twig', array(
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
        $form = $this->createForm(new PresentationUserType(), $entity, array(
            'action' => $this->generateUrl('user_presentation_update', array('id' => $entity->getId())),
            'method' => 'PUT',

        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

}