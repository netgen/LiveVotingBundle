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
            ->add('presenterName')
            ->add('presenterSurname')
            ->add('presentationName')
            ->add('votingEnabled')
            ->add('event', 'entity', array('class'=>'Netgen\LiveVotingBundle\Entity\Event'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Netgen\LiveVotingBundle\Entity\Presentation'
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
