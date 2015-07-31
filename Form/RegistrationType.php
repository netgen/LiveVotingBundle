<?php

namespace Netgen\LiveVotingBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType
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
            ->add('event', null ,array('label'=>'Master Event'))
            ->add('user', 'entity', array(
                'attr' => array('class' => 'form-control'),
                'label' => "User",
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('u')->orderBy('u.email', 'ASC');
                },
                'class' => 'LiveVotingBundle:User',
                'property' => 'email',
                'required'    => false,
                'empty_value' => '(Select user)',
                'empty_data' => null));
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
