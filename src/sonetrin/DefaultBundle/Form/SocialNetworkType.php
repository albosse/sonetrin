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
            ->add('name')
            ->add('url')
            ->add('hashtags', null, array('required' => false))
            ->add('authRequired', null, array('required' => false))
            ->add('language', 'choice', array('choices' => array('en' => 'English','de' => 'German')))
            ->add('username',null,array('required' => false))
            ->add('password','password',array('required' => false))
            ->add('description',null,array('required' => false,
                                               'attr' => array('rows' => '5')))  
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
