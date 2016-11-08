<?php

namespace Netgen\LiveVotingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserEventAssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('event', 'entity', array(
                'class' => 'Netgen\LiveVotingBundle\Entity\Event',
                'label' => 'Assign To Event',
                'required' => false,
                'attr' => array('class'=> 'form-control')
            ))
            ->add('user', 'hidden', array(
                'data' => $options['data']['user_id']
            ));
    }

    public function getName()
    {
        return 'live_voting_user_event_add_form';
    }
}