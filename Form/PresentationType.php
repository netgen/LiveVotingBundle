<?php

namespace Netgen\LiveVotingBundle\Form;

use Doctrine\ORM\EntityRepository;
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
            ->add('name', 'text' ,array('attr' => array('class' => 'form-control')))
            ->add('description', 'textarea' ,array('attr' => array('class' => 'form-control', 'rows' => "7")))
            ->add('image_url', 'file', array(
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
            //->add('joind_in_id', 'text', array('attr' => array('class' => 'form-control'), 'required' => false))
            //->add('event', 'entity', array('class'=>'Netgen\LiveVotingBundle\Entity\Event', 'disabled'=>true))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Netgen\LiveVotingBundle\Service\PresentationService\Record\PresentationRecord',
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
