<?php

namespace Netgen\LiveVotingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label'=>'Event name',
                        'attr' => array(
                            'class' => 'form-control',
                        )
                    ))
            ->add('stateName', 'choice', array(
                'choices' =>array(
                    'PRE' => 'Welcome screen!',
                    'ACTIVE' => 'Start!',
                    'POST' => 'End!'
                ),
                'attr' => array('class'=> 'form-control'),
                'label' =>'Current state'
            ))
            ->add('image', 'file', array(
                'data_class' => null,
                'required' => false,
                'attr' => array('class'=> 'filestyle')
            ))
            ->add('event')
            ->add('begin')
            ->add('end')
            //->add('stateValue', 'text', array('label'=>'Seconds until voting ends'))
        ;

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Netgen\LiveVotingBundle\Entity\Event'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'netgen_livevotingbundle_event';
    }
}
