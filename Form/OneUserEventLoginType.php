<?php

namespace Netgen\LiveVotingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OneUserEventLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('event', 'entity', array(
                'class' => 'Netgen\LiveVotingBundle\Entity\Event',
                'choices' => $options['data']['accessible_events'],
                'label' => 'Send For Event',
                'required' => true,
                'attr' => array('class'=> 'form-control')
            ))
            ->add('user', 'hidden', array(
                'data' => $options['data']['user_id']
            ));
    }

    public function getName()
    {
        return 'live_voting_user_send_one_login_email';
    }
}