<?php

namespace sonetrin\DefaultBundle\Twig\Extensions;

class DefaultExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'password' => new \Twig_Filter_Method($this, 'passwordFilter'),
        );
    }

    public function passwordFilter($password)
    {
        return str_repeat('&#8226', count($password));
    }

    public function getName()
    {
        return 'default_extension';
    }
}