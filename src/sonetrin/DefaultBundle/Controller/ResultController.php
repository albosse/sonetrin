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
        $assocArray = $this->matchKeywords($search->getResult());
        return array('entity' => $search,
            'associations' => $assocArray);
    }

    /**
     * @Route("/{id}/show/{count}", name="result_show", requirements={"id" = "\d+"}, defaults={"count" = 50})
     * @Template()
     */
    public function showAction(Result $id, $count)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository('sonetrinDefaultBundle:Result')->find($id->getId());
//       die(print_r($result->getFile()));

        if ($count > count($result->getFile()))
        {
            $count = count($result->getFile());
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

    public function matchKeywords($results)
    {
        $assocArray = array('positive' => 0, 'negative' => 0, 'unknown' => 0);

        $em = $this->getDoctrine()->getManager();
        $keywords = $em->getRepository('sonetrinDefaultBundle:Keyword')->findAll();

        foreach ($results as $id)
        {
            $file = $id->getFile();

            foreach ($file as $entity)
            {
                foreach ($keywords as $keyword)
                {
                    if (preg_match('|' . $keyword->getEnglish() . ' |Ui',$entity->text))
                    {
                       $assocArray[$keyword->getAssociation()]++;
                       //Merken welcher Eintrag Positiv und wel ist
                       break;
                    }                                      

                }
            }
        }
        return $assocArray;
    }

}
