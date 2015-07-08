<?php

namespace Netgen\LiveVotingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserDataType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('gender', 'choice', array(
                'choices' => array('Male', 'Female'),
                'required' => false
            ))
            ->add('country', 'text', array('required' => false))
            ->add('city', 'text', array('required' => false))
            ->add('tshirt', 'choice', array(
                'choices' => array('Female S','Female M','Female L','Male S','Male M','Male L','Male XL','Male XXL','Male XXXL'),
                'required' => false,
                'label' => 'T-shirt'
            ))
            ->add('foodPreference', 'choice', array(
                'choices' => array ('Everything','No seafood','Vegeterian','Gluten free','Lactose free'),
                'required' => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Netgen\LiveVotingBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'netgen_livevotingbundle_user';
    }
}
