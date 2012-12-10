<?php

namespace sonetrin\DefaultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use sonetrin\DefaultBundle\Entity\SocialNetwork;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array('label' => 'Search'))
            ->add('startDate',null,array(
                    'years' => range(2000,date('Y',time()))
                ))
            ->add('endDate',null,array(
                    'years' => range(2000,date('Y',time())),
                    'data' => new \DateTime()
                ))
            ->add('semantic',null,array('required' => false))
            ->add('socialNetwork','entity', array(
                    'class' => 'sonetrinDefaultBundle:SocialNetwork',
                    'property' => 'name',
                    'multiple' => true))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'sonetrin\DefaultBundle\Entity\Search'
        ));
    }

    public function getName()
    {
        return 'sonetrin_defaultbundle_searchtype';
    }
}
