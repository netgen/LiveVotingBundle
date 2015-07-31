<?php

namespace Netgen\LiveVotingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('devLevel', 'choice', array(
                'choices' => array('Beginner'=>'Beginner', 'Intermediate'=>'Intermediate', 'Advanced'=>'Advanced'),
                'label' => 'Developer level'
            ))
            ->add('arrivalTime', "datetime", array(
                "years" => range(date('Y') - 0, date('Y') + 5)
            ))
            ->add('departureTime', "datetime", array(
                "years" => range(date('Y') - 0, date('Y') + 5)
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Netgen\LiveVotingBundle\Entity\Registration'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'netgen_livevotingbundle_registration';
    }
}
