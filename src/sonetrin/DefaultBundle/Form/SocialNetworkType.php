<?php

namespace sonetrin\DefaultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SocialNetworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array('attr' => array('class' => 'span5')))
            ->add('url',null,array('attr' => array('class' => 'span5')))
            ->add('authRequired', null, array('required' => false))
            ->add('api_key',null,array('required' => false,'attr' => array('class' => 'span5')))
            ->add('description',null,array('required' => false,
                                               'attr' => array('rows' => '8','class' => 'span5')))  
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'sonetrin\DefaultBundle\Entity\SocialNetwork'
        ));
    }

    public function getName()
    {
        return 'sonetrin_defaultbundle_socialnetworktype';
    }
}
