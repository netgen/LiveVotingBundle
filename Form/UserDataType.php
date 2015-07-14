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
            /*->add('email')*/
            ->add('gender', 'choice', array(
                'choices' => array('Male'=>'Male', 'Female'=>'Female'),
                'required' => false
            ))
            ->add('country', 'text', array('required' => false))
            ->add('city', 'text', array('required' => false))
            ->add('tshirt', 'choice', array(
                'choices' => array('Female S'=>'Female S',
                    'Female M'=>'Female M',
                    'Female L'=>'Female L',
                    'Female XL'=>'Female XL',
                    'Male S'=>'Male S',
                    'Male M'=>'Male M',
                    'Male L'=>'Male L',
                    'Male XL'=>'Male XL',
                    'Male XXL'=>'Male XXL',
                    'Male XXXL'=>'Male XXXL'),
                'required' => false,
                'label' => 'T-shirt'
            ))
            ->add('foodPreference', 'choice', array(
                'choices' => array ('Everything'=>'Everything',
                    'No seafood'=>'No seafood',
                    'Vegeterian'=>'Vegeterian',
                    'Gluten free'=>'Gluten free',
                    'Lactose free'=>'Lactose free'),
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
