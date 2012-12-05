<?php

namespace sonetrin\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use sonetrin\DefaultBundle\Entity\Search;
use sonetrin\DefaultBundle\Form\SearchType;
use sonetrin\DefaultBundle\Module\TwitterModule;

/**
 * Search controller.
 *
 * @Route("/search")
 */
class SearchController extends Controller {

    /**
     * Lists all Search entities.
     *
     * @Route("/", name="search")
     * @Template()
     */
    public function indexAction() {
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
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);

        if (!$entity) {
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
    public function newAction() {
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
    public function createAction() {
        $entity = new Search();
        $request = $this->getRequest();
        $form = $this->createForm(new SearchType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
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
     * Displays a form to edit an existing Search entity.
     *
     * @Route("/{id}/edit", name="search_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);

        if (!$entity) {
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
    public function updateAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Search entity.');
        }

        $editForm = $this->createForm(new SearchType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
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
    public function deleteAction($id) {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

//        if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Search entity.');
        }

        $em->remove($entity);
        $em->flush();
//        }
        $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');
        return $this->redirect($this->generateUrl('search'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
     * Search networks for the created query.
     *
     * @Route("/run/{id}", name="search_run", requirements={"id" = "\d+"})
     * @Template()
     */
    public function runAction($id) {
        $em = $this->getDoctrine()->getManager();
        $search = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);
        
        $tm = new TwitterModule($em,$search);
        $result_twitter = $tm->findTwitterResults();
        
        if($tm->saveResult())
        {
             $this->get('session')->getFlashBag()->add('notice', 'Your results were saved!');
        }else
        {
             $this->get('session')->getFlashBag()->add('warning', 'Error: Your results were not saved!');
        }
        
       
        return array('entity' => $search,
            'result_twitter' => $result_twitter);
    }
    
    /**
     * Results
     *
     * @Route("/results/{id}", name="search_results", requirements={"id" = "\d+"})
     * @Template()
     */
    public function resultsAction($id) {
        $em = $this->getDoctrine()->getManager();
        $search = $em->getRepository('sonetrinDefaultBundle:Search')->find($id);
        
        return array('entity' => $search);
    }
   
}
