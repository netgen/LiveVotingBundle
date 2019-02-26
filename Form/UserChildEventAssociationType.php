<?php

namespace Netgen\LiveVotingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserChildEventAssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityDefinitionArray = array(
            'class' => 'Netgen\LiveVotingBundle\Entity\Event',
            'label' => 'Assign To Secondary Event - ' . $options['data']['main_event_name'],
            'required' => false,
            'attr' => array('class' => 'form-control')
        );

        if ( array_key_exists( 'choices', $options['data'] ) )
        {
            $entityDefinitionArray['choices'] = $options['data']['choices'];
        }

        $builder
            ->add('event', 'entity', $entityDefinitionArray )
            ->add('user', 'hidden', array(
                'data' => $options['data']['user_id']
            ));
    }

    public function getName()
    {
        return 'live_voting_user_child_event_add_form';
    }
}