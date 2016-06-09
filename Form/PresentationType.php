<?php

namespace Netgen\LiveVotingBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PresentationType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('presentation_name', 'text' ,array('attr' => array('class' => 'form-control')))
            ->add('presenter_name', 'text' ,array('attr' => array('class' => 'form-control')))
            ->add('presenter_surname', 'text' ,array('attr' => array('class' => 'form-control')))

            ->add('description', 'textarea' ,array('attr' => array('class' => 'form-control', 'rows' => "7")))
            ->add('image', 'file', array(
                'data_class' => null,
                'required' => false
            ))
            ->add('globalBrake', 'checkbox', array('attr' => array('class' => 'form-control'), "label" => "Global break (lunch, pause, etc.)", 'required' => false))
            ->add('hall', 'text' ,array('attr' => array('class' => 'form-control')))
            ->add('begin', 'datetime', array(
                "years" => range(date('Y') - 0, date('Y') + 5)
            ))
            ->add('end', 'datetime', array(
                "years" => range(date('Y') - 0, date('Y') + 5)
            ))
            ->add('user', 'entity', array(
                // query choices from this entity
                'class' => 'Netgen\LiveVotingBundle\Entity\User',

                // use the User.username property as the visible option string
                'property' => 'email'));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Netgen\LiveVotingBundle\Entity\Presentation',
            'attr' => array('style'=>'width:300px;margin-left:10px;', 'role'=>'form')
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'netgen_livevotingbundle_presentation';
    }
}
