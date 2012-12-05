<?php

namespace sonetrin\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default_index")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
           
     /**
     * @Route("/about", name="default_about")
     * @Template()
     */
    public function aboutAction()
    {
        return array();
    }
}
