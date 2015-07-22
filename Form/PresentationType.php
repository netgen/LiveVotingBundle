<?php

namespace Netgen\LiveVotingBundle\Form;

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
            ->add('presenterName', 'text' ,array('attr' => array('class' => 'form-control')))
            ->add('presenterSurname', 'text' ,array('attr' => array('class' => 'form-control')))
            ->add('presentationName', 'text' ,array('attr' => array('class' => 'form-control')))
            ->add('description', 'textarea' ,array('attr' => array('class' => 'form-control', 'rows' => "7")))
            ->add('image', 'file', array(
                'data_class' => null,
                'required' => false
            ))
            ->add('hall', 'text' ,array('attr' => array('class' => 'form-control')))
            ->add('user', 'entity' ,array('attr' => array('class' => 'form-control'), 'class' => 'LiveVotingBundle:User', 'property' => 'email'))
            ->add('begin')
            ->add('end')
            ->add('joind_in_id', 'text', array('attr' => array('class' => 'form-control')))
            //->add('event', 'entity', array('class'=>'Netgen\LiveVotingBundle\Entity\Event', 'disabled'=>true))
        ;
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
