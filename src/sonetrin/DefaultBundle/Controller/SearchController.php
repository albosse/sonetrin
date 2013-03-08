<?php

namespace sonetrin\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Form\SearchType;
use sonetrin\DefaultBundle\Module\TwitterModule;
use sonetrin\DefaultBundle\Module\GooglePlusModule;
use sonetrin\DefaultBundle\Module\FacebookModule;

/**
 * Search controller.
 *
 * @Route("/search")
 */
class SearchController extends Controller
{
    /**
     * Lists all Search entities.
     *
     * @Route("/", name="search")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('sonetrinDefaultBundle:Search')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Search entity.
     *
     * @Route("/{id}/show", name="search_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Search entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Search entity.
     *
     * @Route("/new", name="search_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Search();
        $form = $this->createForm(new SearchType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Search entity.
     *
     * @Route("/create", name="search_create")
     * @Method("post")
     * @Template("sonetrinDefaultBundle:Search:new.html.twig")
     */
    public function createAction()
    {
        $entity = new Search();
        $request = $this->getRequest();
        $form = $this->createForm(new SearchType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');
            return $this->redirect($this->generateUrl('search'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }
    
     /**
     * Creates a new Search entity.
     *
     * @Route("/createShort", name="search_shortQuery")
     */
    public function createShortQueryAction()
    {
        if(false === isset($_POST['query']))
        {
            return $this->redirect($this->generateUrl('default_index'));
        }
          
        $query = htmlspecialchars($_POST['query']);
        $em = $this->getDoctrine()->getManager();
        
        $search  = $em->getRepository('sonetrinDefaultBundle:Search')->findOneBy(array('name' => $query ));
         
        if(true === is_null($search))
        {
            $socialNetworks = $em->getRepository('sonetrinDefaultBundle:SocialNetwork')->findAll();        
            $search = new Search();
            $search->setName($query);

            foreach($socialNetworks as $sn)
            {
               $search->setSocialNetwork($sn);
            }

            $em->persist($search);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('search_collect', array('id' => $search->getId())));                  
    }
       
    /**
     * Displays a form to edit an existing Search entity.
     *
     * @Route("/{id}/edit", name="search_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Search entity.');
        }

        $editForm = $this->createForm(new SearchType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Search entity.
     *
     * @Route("/{id}/update", name="search_update")
     * @Method("post")
     * @Template("sonetrinDefaultBundle:Search:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Search entity.');
        }

        $editForm = $this->createForm(new SearchType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid())
        {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');
            return $this->redirect($this->generateUrl('search'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Search entity.
     *
     * @Route("/{id}/delete", name="search_delete")
     * @Method({"GET","POST"})
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

//        if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Search entity.');
        }

        $em->remove($entity);
        $em->flush();
//        }
        $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');
        return $this->redirect($this->generateUrl('search'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }
    
     /**
     * Search networks for the created query.
     *
     * @Route("/collect/{id}", name="search_collect", requirements={"id" = "\d+"})
     * @Template()
     */
    public function collectAction(Search $id)
    {      
        return array('entity' => $id);      
    }

    /**
     * Search networks for the created query.
     *
     * @Route("/run/{id}", name="search_run", requirements={"id" = "\d+"})
     */
    public function runAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $search = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);
        
        foreach($search->getSocialNetwork() as $sn)
        {
            switch($sn->getName())
            {
                case 'twitter':
                   $status = $this->getTwitterResults($search);    
                break;  
                case 'googleplus':
                   $status = $this->getGooglePlusResults($search);
                break;
                case 'facebook':
                   $status = $this->getFacebookResults($search);
                break;
            }
        }
       
        //Get new search entity with new results
        $em->refresh($search);
        return $this->redirect($this->generateUrl('result_analyze', array('search' => $search->getId())));  
    }
    
     /**
     * Search networks for the created query with ajax.
     *
     * @Route("/run_ajax/{id}", name="search_runAjax", requirements={"id" = "\d+"})
     */
    public function runAjaxAction($id)
    {    
        $em = $this->getDoctrine()->getManager();
        $search = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);
               
        foreach($search->getSocialNetwork() as $sn)
        {
            switch($sn->getName())
            {
                case 'twitter':
                   $status = $this->getTwitterResults($search);    
                break;  
                case 'googleplus':
                   $status = $this->getGooglePlusResults($search);
                break;
                case 'facebook':
                   $status = $this->getFacebookResults($search);
                break;
            }
        }
        
        $em->refresh($search);
        return $this->forward('sonetrinDefaultBundle:Result:analyzeResults', array('search' =>  $search->getId()));
        
    }

    private function getTwitterResults($search)
    {
        $em = $this->getDoctrine()->getManager();
        $tm = new TwitterModule($em, $search);
        $tm->findResults();
    }

    private function getGooglePlusResults($search)
    {
        $em = $this->getDoctrine()->getManager();
        $gpm = new GooglePlusModule($em, $search);
        $gpm->findResults();
    }
    
    private function getFacebookResults($search)
    {
        $em = $this->getDoctrine()->getManager();
        $fbm = new FacebookModule($em, $search);
        $fbm->findResults();
    }
}
