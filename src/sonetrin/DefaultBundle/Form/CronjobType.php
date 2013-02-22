<?php

namespace sonetrin\DefaultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CronjobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search','entity', array(
                    'class' => 'sonetrinDefaultBundle:Search',
                    'property' => 'name',
                    'multiple' => false))
            ->add('frequency','choice',array('choices' => array('hourly' => 'hourly',
                                                                'daily'  => 'daily',
                                                               'monthly' =>'monthly')))
            ->add('active',null,array('required' => false))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'sonetrin\DefaultBundle\Entity\Cronjob'
        ));
    }

    public function getName()
    {
        return 'sonetrin_defaultbundle_cronjobtype';
    }
}
