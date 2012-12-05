<?php

namespace sonetrin\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use sonetrin\DefaultBundle\Entity\SocialNetwork;
use sonetrin\DefaultBundle\Form\SocialNetworkType;

/**
 * SocialNetwork controller.
 *
 * @Route("/socialnetwork")
 */
class SocialNetworkController extends Controller
{
    /**
     * Lists all SocialNetwork entities.
     *
     * @Route("/", name="socialnetwork")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('sonetrinDefaultBundle:SocialNetwork')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a SocialNetwork entity.
     *
     * @Route("/{id}/show", name="socialnetwork_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:SocialNetwork')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SocialNetwork entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new SocialNetwork entity.
     *
     * @Route("/new", name="socialnetwork_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new SocialNetwork();
        $form   = $this->createForm(new SocialNetworkType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new SocialNetwork entity.
     *
     * @Route("/create", name="socialnetwork_create")
     * @Method("post")
     * @Template("sonetrinDefaultBundle:SocialNetwork:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new SocialNetwork();
        $request = $this->getRequest();
        $form    = $this->createForm(new SocialNetworkType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('socialnetwork'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing SocialNetwork entity.
     *
     * @Route("/{id}/edit", name="socialnetwork_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:SocialNetwork')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SocialNetwork entity.');
        }

        $editForm = $this->createForm(new SocialNetworkType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing SocialNetwork entity.
     *
     * @Route("/{id}/update", name="socialnetwork_update")
     * @Method("post")
     * @Template("sonetrinDefaultBundle:SocialNetwork:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:SocialNetwork')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SocialNetwork entity.');
        }

        $editForm   = $this->createForm(new SocialNetworkType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');
            return $this->redirect($this->generateUrl('socialnetwork'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a SocialNetwork entity.
     *
     * @Route("/{id}/delete", name="socialnetwork_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('sonetrinDefaultBundle:SocialNetwork')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SocialNetwork entity.');
            }

            $em->remove($entity);
            $em->flush();
        }
        $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');
        return $this->redirect($this->generateUrl('socialnetwork'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
