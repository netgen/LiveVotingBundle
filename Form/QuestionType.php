<?php

namespace Netgen\LiveVotingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionType extends AbstractType
{
	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('question', 'text', array(
					'attr' => array(
						'class' => 'form-control',
					),
				))
			->add('question_type', 'choice', array(
				    'choices'   => array(
				        '0'   => 'Voting (1-5)',
				        '1' => 'Answer (Yes-No)',
				    ),
				    'attr' => array('class'=> 'form-control')
			));

		$builder->add('votingEnabled');
		$builder->add('submit', 'submit', array('label' => 'Save questions', 'attr' => array('class' => 'btn btn-large btn-primary')));
	}

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Netgen\LiveVotingBundle\Entity\Question',
            'attr' => array('class' => 'form-horizontal', 'role' => 'form')
        ));
    }

	public function getName()
	{
		return 'netgen_livevotingbundle_question';
	}
} 

?>