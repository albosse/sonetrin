<?php

namespace sonetrin\DefaultBundle\Extensions\Twig;
use Symfony\Component\DependencyInjection\Container;

class DefaultExtension extends \Twig_Extension
{
    protected $container;
 
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    /**
     * 
     * @return type array
     */
    public function getGlobals()
    {               
        $searches = $this->container->get('doctrine')->getRepository('sonetrinDefaultBundle:Search')->findExistingSearchNames();
        
        if(true === empty($searches)){
            $searches = "";
        }
        
        return array('_searchNames' => json_encode($searches));
    }


    public function getName()
    {
        return 'search_extension';
    }
}