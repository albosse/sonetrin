<?php

namespace sonetrin\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Entity\Result;
use sonetrin\DefaultBundle\Entity\Keyword;

/**
 * @Route("/results")
 */
class ResultController extends Controller
{

    /**
     * Displays the results for a specific sesrch
     * 
     * @Route("/{search}", name="result", requirements={"search" = "\d+"})
     * @Template()
     */
    public function indexAction(Search $search)
    {       
        return array('entity' => $search);
    }

    /**
     * @Route("/{id}/show/{count}", name="result_show", requirements={"id" = "\d+"}, defaults={"count" = 50})
     * @Template()
     */
    public function showAction(Result $id, $count)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('sonetrinDefaultBundle:Result')->find($id->getId());


        if ($count > count($result->getItem()))
        {
            $count = count($result->getItem());
        }
        return array('result' => $result,
            'count' => $count);
    }

    /**
     * @Route("/{id}/delete", name="result_delete", requirements={"id" = "\d+"})
     * @Template()
     */
    public function deleteAction(Result $id)
    {
        if (false === is_null($id))
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($id);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');
        }
        return $this->redirect($this->generateUrl('result', array('search' => $id->getSearch()->getId())));
    }
     
    /**
     * @Route("/test/{search}", name="result_test")
     * @Template()
     */
    public function testAction(Search $search)
    {
        require_once __DIR__ . '/../Resources/api/opinion-mining/Example.php';

        $sentences = array();
        $results = $search->getResult();
     
        foreach ($results as $result)
        {
            $file = $result->getFile();
      
            foreach ($file as $entity)
            {
                $sentences[] = $entity->text;
            }
        }
        
        $ex = new \Example();
        $ex->runFile($sentences);
        return array();
    }
    
     /**
     * @Route("/cake", name="result_cake_graph")
     * 
     */
    public function cake()
    {
        include_once (__DIR__ . "/../Resources/api/jpgraph/src/jpgraph.php");
        include_once (__DIR__ . "/../Resources/api/jpgraph/src/jpgraph_pie.php");

        $data = array(60, 40);

        $graph = new \PieGraph(300, 200);
        $graph->SetShadow();

        $graph->title->SetFont(FF_FONT1, FS_BOLD,20);

        $p1 = new \PiePlot($data);
        $p1->SetLegends(array('pos','neg'));     
        
        $graph->legend->SetPos(0.0,0.1,'right','center');
        $graph->legend->SetFont(FF_FONT1, FS_BOLD, 24);

        $graph->Add($p1);
        $graph->Stroke();
    }
}
