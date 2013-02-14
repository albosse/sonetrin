<?php

namespace sonetrin\DefaultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeywordType extends AbstractType
{
    private $default;
    
    public function __construct($assoc = null)
    {
        if(isset($assoc))
        {
            $this->default = $assoc;
        }
        
    }
        
        
    public function buildForm(FormBuilderInterface $builder, array $options)
        {
        $builder
            ->add('expression')
            ->add('language','choice',array('choices' => array('en' => 'English', 'de' => 'German'),
                                               'data' => $this->default) )
            ->add('association','choice',array('choices' => array('positive' => 'Positive', 'negative' => 'Negative'),
                                               'data' => $this->default) )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'sonetrin\DefaultBundle\Entity\Keyword'
        ));
    }

    public function getName()
    {
        return 'sonetrin_defaultbundle_keywordtype';
    }
}
