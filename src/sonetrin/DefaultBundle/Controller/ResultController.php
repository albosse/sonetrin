<?php

namespace sonetrin\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Entity\Result;
use sonetrin\DefaultBundle\Entity\Keyword;
use sonetrin\DefaultBundle\Entity\Item;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/results")
 */
class ResultController extends Controller
{

    /**
     * Displays the results for a specific sesrch
     * 
     * @Route("/{search}/{filter}", name="result", requirements={"search" = "\d+"}, defaults={"filter" = false})
     * @Template()
     */
    public function indexAction(Search $search, $filter)
    {

        $em = $this->getDoctrine()->getManager();
        $randomItems = $em->getRepository('sonetrinDefaultBundle:Item')->findAllItemsBySearch($search, $filter);
        $sentimentCount = $em->getRepository('sonetrinDefaultBundle:Result')->findRecordsSentiments($search->getId());

        return array('entity' => $search, 'randomItems' => $randomItems, 'sentimentCount' => $sentimentCount);
    }

    /**
     * @Route("/show/{id}/limit/{count}/{filter}", name="result_show", requirements={"id" = "\d+"}, defaults={"count" = 50, "filter" = false})
     * @Template()
     */
    public function showAction(Result $id, $count, $filter)
    {
        $em = $this->getDoctrine()->getManager();

        $result = $em->getRepository('sonetrinDefaultBundle:Result')->find($id->getId());

        if ($filter == 'positive' || $filter == 'negative')
        {
            $items = $em->getRepository('sonetrinDefaultBundle:Item')->findBy(array('result' => $id->getId(), 'sentiment' => $filter));
        } else
        {
            $items = $em->getRepository('sonetrinDefaultBundle:Item')->findBy(array('result' => $id->getId()));
        }

        if ($filter == 'random')
        {
            shuffle($items);
        }

        $countItems = count($items);
        if ($count > $countItems)
        {
            $count = $countItems;
        }

        return array('entity' => $result,
            'items' => $items,
            'count' => $count);
    }

    /**
     * @Route("/delete/{id}", name="result_delete", requirements={"id" = "\d+"})
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
     * @Route("/deleteItem/item/{item}", name="result_deleteItem", requirements={"item" = "\d+"})
     * @Template()
     */
    public function deleteItemAction($item)
    {
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository('sonetrinDefaultBundle:Item')->findOneBy(array('id' => $item));

        if (false === is_null($item))
        {
            $em->remove($item);
            $em->flush();
            
            $response = 'Item deleted!';
        }
        else
        {
            $response = 'Item not found';
        }
        return new Response($response);
    }

    /**
     * Analyzes results based on a list of positive and negative keywords
     * 
     * If there are more positive than negative keywords, 
     * the message is considered to be positive and counterwise
     * 
     * @param \sonetrin\DefaultBundle\Entity\Search $search
     * 
     * @Route("/analyze/{search}", name="result_analyze")
     */
    public function analyzeResultsAction(Search $search)
    {
        $em = $this->getDoctrine()->getManager();

        $items = $em->getRepository('sonetrinDefaultBundle:Item')->findBy(array('search' => $search->getId()));
        $keywords = $em->getRepository('sonetrinDefaultBundle:Keyword')->findBy(array('language' => $search->getLanguage()));
        
        if(true === is_null($keywords)){
            return new Response('There are no keywords fitting the language for your search (' . $search->getLanguage() .')!');
        }

        foreach ($items as $item)
        {
            //if item already has a sentiment
            if (false === is_null($item->getSentiment()))
            {
                continue;
            }

            $message = $item->getMessage();

            //reset counter
            $pos = 0;
            $neg = 0;

            foreach ($keywords as $keyword)
            {
                if (true == preg_match('| [#]*' . preg_quote($keyword->getExpression()) . '\b|i', $message))
                {
                    if ($keyword->getAssociation() == 'positive')
                    {
                        $pos++;
                    } else
                    {
                        $neg++;
                    }
                }
            }
            if ($pos > $neg)
            {
                $item->setSentiment('positive');
            } elseif ($pos < $neg)
            {
                $item->setSentiment('negative');
            }
            
//            echo $message . '<br />' . 'pos: ' . $pos  . '<br />' . 'neg: ' . $neg . '<br /><br/>';
        }
//        die;
        //Save changes
        $em->flush();

        return new Response('finished');
//         return $this->redirect($this->generateUrl('result', array('search' => $search->getId())));
    }

    /**
     * @Route("/removeSentiments/{search}", name="result_removeSentiments")
     */
    public function removeSentimentsAction(Search $search)
    {
        $em = $this->getDoctrine()->getManager();
        $results = $em->getRepository('sonetrinDefaultBundle:Result')->findBy(array('search' => $search->getId()));

        foreach ($results as $result)
        {
            foreach ($result->getItem() as $item)
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

        foreach ($results as $result)
        {
            $em->remove($result);
        }
        $em->flush();

        return $this->redirect($this->generateUrl('search'));
    }

    /**
     * Analyzes results based on a list of positive and negative keywords
     * 
     * If there are more positive than negative keywords, 
     * the message is considered to be positive and counterwise
     * 
     * @param \sonetrin\DefaultBundle\Entity\Search $search
     * 
     * @Route("/analyzeOne/{message}", name="result_analyzeOne")
     */
    public function analyzeOneResultAction($message)
    {
        $em = $this->getDoctrine()->getManager();
        $keywords = $em->getRepository('sonetrinDefaultBundle:Keyword')->findAll();

        //reset counter
        $pos = 0;
        $neg = 0;

        echo $message . ' : <br />';

        foreach ($keywords as $keyword)
        {
            if (true == preg_match('| [#]*' . preg_quote($keyword->getExpression()) . '\b|i', $message))
            {
                if ($keyword->getAssociation() == 'positive')
                {
                    echo preg_quote($keyword->getExpression()) . ': positive <br />';
                    $pos++;
                } else
                {
                    echo preg_quote($keyword->getExpression()) . ': negative <br />';
                    $neg++;
                }
            }
        }

        echo '<br /><br />';
        echo 'Positive: ' . $pos . '<br />';
        echo 'Negative: ' . $neg;

        die;
        return array();
    }
    
    
    /**
     * @Route("/log/{id}", name="result_log", requirements={"id" = "\d+"})
     * @Template()
     */
    public function logAction(Result $id)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('sonetrinDefaultBundle:Result')->find($id->getId());
        
        return array('entity' => $result);
    }

}
