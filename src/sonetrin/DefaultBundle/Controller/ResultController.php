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
        $em = $this->getDoctrine()->getManager();
        $randomItems = $em->getRepository('sonetrinDefaultBundle:Item')->findAllItemsBySearch($search);
        $sentimentCount = $em->getRepository('sonetrinDefaultBundle:Result')->findRecordsSentiments($search->getId());
        
        return array('entity' => $search, 'randomItems' => $randomItems, 'sentimentCount' => $sentimentCount);
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
     * 
     * @param \sonetrin\DefaultBundle\Entity\Search $search
     * 
     * @Route("/analyze/{search}", name="result_analyze")
     */
    public function analyzeResultsAction(Search $search)
    {
        $em = $this->getDoctrine()->getManager();

        $results = $em->getRepository('sonetrinDefaultBundle:Result')->findBy(array('search' => $search->getId()));
        $keywords = $em->getRepository('sonetrinDefaultBundle:Keyword')->findAll();


        foreach ($results as $sn_result)
        {
            $items = $sn_result->getItem();

            foreach ($items as $item)
            {
                //if item already has a sentiment
                if(false === is_null($item->getSentiment()))
                {
                    continue;
                }
                
                $message = $item->getMessage();

                foreach ($keywords as $keyword)
                {
                    $pos = 0;
                    $neg = 0;
                    
                    if (true == preg_match('|' .$keyword->getEnglish()  . '|i', $message))
                    {
                        if($keyword->getAssociation() == 'positive')
                        {
                            $pos++;
                        }else
                        {
                            $neg++;
                        }
                    }
                     if($pos > $neg)
                     {
                         $item->setSentiment('positive');
                     }
                     elseif($pos < $neg)
                     {
                         $item->setSentiment('negative');
                     }                  
                }           
            }
            //Save changes
           $em->flush();
        }
        
         return $this->redirect($this->generateUrl('result', array('search' => $search->getId())));
    }
    
    /**
     * @Route("/removeSentiments/{search}", name="result_removeSentiments")
     */
    public function removeSentimentsAction(Search $search)
    {
        $em = $this->getDoctrine()->getManager();
        $results = $em->getRepository('sonetrinDefaultBundle:Result')->findBy(array('search' => $search->getId()));
        
        foreach($results as $result)
        {
            foreach($result->getItem() as $item)
            {
                $item->setSentiment(null);
            }
        }
  
        $em->flush();
        
         return $this->redirect($this->generateUrl('result', array('search' => $search->getId())));
    }
    
    /**
     * @Route("/removeUndefined", name="result_removeUndefinedResults")
     */
    public function removeUndefinedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $results = $em->getRepository('sonetrinDefaultBundle:Item')->findBy(array('result' => null));
        
        foreach($results as $result)
        {
            $em->remove($result);
        }
        $em->flush();
        
        return $this->redirect($this->generateUrl('search'));
              
    }
}
