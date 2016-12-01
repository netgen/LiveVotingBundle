<?php

namespace Netgen\LiveVotingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UsersByEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'event',
                'entity',
                array(
                    'class' => 'Netgen\LiveVotingBundle\Entity\Event',
                    'choices' => $options['data']['accessible_events'],
                    'label' => 'Show users for event',
                    'required' => true,
                    'attr' => array( 'class' => 'form-control' )
                )
            )
            ->add(
                'submit',
                'submit',
                array(
                    'label' => 'Select'
                )
            )
        ;
    }

    public function getName()
    {
        return 'live_voting_bundle_users_by_event_type';
    }
}