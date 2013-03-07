<?php

namespace sonetrin\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use sonetrin\DefaultBundle\Entity\Cronjob;
use sonetrin\DefaultBundle\Form\CronjobType;
use Symfony\Component\HttpFoundation\Response;


/**
 * Cronjob controller.
 *
 * @Route("/cronjob")
 */
class CronjobController extends Controller
{
    /**
     * Lists all Cronjob entities.
     *
     * @Route("/", name="cronjob")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('sonetrinDefaultBundle:Cronjob')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a Cronjob entity.
     *
     * @Route("/{id}/show", name="cronjob_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:Cronjob')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Cronjob entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Cronjob entity.
     *
     * @Route("/new", name="cronjob_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Cronjob();
        $form = $this->createForm(new CronjobType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Cronjob entity.
     *
     * @Route("/create", name="cronjob_create")
     * @Method("post")
     * @Template("sonetrinDefaultBundle:Cronjob:new.html.twig")
     */
    public function createAction()
    {
        $entity = new Cronjob();
        $request = $this->getRequest();
        $form = $this->createForm(new CronjobType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cronjob_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Cronjob entity.
     *
     * @Route("/{id}/edit", name="cronjob_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:Cronjob')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Cronjob entity.');
        }

        $editForm = $this->createForm(new CronjobType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Cronjob entity.
     *
     * @Route("/{id}/update", name="cronjob_update")
     * @Method("post")
     * @Template("sonetrinDefaultBundle:Cronjob:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:Cronjob')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Cronjob entity.');
        }

        $editForm = $this->createForm(new CronjobType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid())
        {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cronjob_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Cronjob entity.
     *
     * @Route("/{id}/delete", name="cronjob_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('sonetrinDefaultBundle:Cronjob')->find($id);

            if (!$entity)
            {
                throw $this->createNotFoundException('Unable to find Cronjob entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cronjob'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
     * Runs a Cronjob .
     *
     * @Route("/run", name="cronjob_run")
     * @Template()
     */
    public function runAction()
    {
        $em = $this->getDoctrine()->getManager();       
        $cronjobs = $em->getRepository('sonetrinDefaultBundle:Cronjob')->findBy(array('active' => true));
        
        $cronlog = array();
        foreach ($cronjobs as $cronjob)
        {
            $now = new \DateTime();
            $lastRun = $cronjob->getLastRun();
            if (is_null($lastRun))
            {
                $lastRun = new \DateTime('2000-01-01');
            }

            switch ($cronjob->getFrequency())
            {
                case 'hourly':

                    $mustRun = $lastRun->add(new \DateInterval('PT1H'));

                    if ($mustRun < $now)
                    {
                        //perform cronjob
                        $response = $this->forward('sonetrinDefaultBundle:Search:runAjax', array('id' => $cronjob->getSearch()->getId()));
                        //update lastRun
                        $cronjob->setLastRun($now);
                        //Logfile
                        $cronlog[] = 'Cronjob ' . $cronjob->getSearch()->getName() . ' done.';
                    }
                    break;

                case 'daily':
                    $mustRun = $lastRun->add(new \DateInterval('P1D'));

                    if ($mustRun < $now)
                    {
                        //perform cronjob
//                        $this->searchAction($cronjob->getSearch()->getId());
                        $response = $this->forward('sonetrinDefaultBundle:Search:runAjax', array('id' => $cronjob->getSearch()->getId()));
                        //update lastRun
                        $cronjob->setLastRun($now);
                        //Logfile
                        $cronlog[] = 'Cronjob ' . $cronjob->getSearch()->getName() . ' done.';
                    }
                    break;

                case 'monthly':
                    $mustRun = $lastRun->add(new \DateInterval('P1M'));

                    if ($lastRun < $now)
                    {
                        //perform cronjob
//                       $this->searchAction($cronjob->getSearch()->getId());
                        $response = $this->forward('sonetrinDefaultBundle:Search:runAjax', array('id' => $cronjob->getSearch()->getId()));
                        //update lastRun
                        $cronjob->setLastRun($now);
                        //Logfile
                        $cronlog[] = 'Cronjob ' . $cronjob->getSearch()->getName() . ' done.';
                    }

                    break;
            }
        }
        $em->flush();

        if (empty($cronlog))
        {
            $cronlog[] = "No cronjob needed to be run.";
        }
        
        $return = json_encode($cronlog); //jscon encode the array
        return new Response($return, 200, array('Content-Type' => 'application/json')); //make sure it has the correct content type
    }
    
    
    
    public function cronjobRun(\Symfony\Component\DependencyInjection\Container $container)
    {   
        $em =  $container->get('doctrine')->getManager();             
        $cronjobs = $em->getRepository('sonetrinDefaultBundle:Cronjob')->findBy(array('active' => true));
    
        $cronlog = array();
        foreach ($cronjobs as $cronjob)
        {
            $now = new \DateTime();
            $lastRun = $cronjob->getLastRun();
            if (is_null($lastRun))
            {
                $lastRun = new \DateTime('2000-01-01');
            }

            switch ($cronjob->getFrequency())
            {
                case 'hourly':

                    $mustRun = $lastRun->add(new \DateInterval('PT1H'));

                    if ($mustRun < $now)
                    {
                        //perform cronjob
                        $response = $this->forward('sonetrinDefaultBundle:Search:runAjax', array('id' => $cronjob->getSearch()->getId()));
                        //update lastRun
                        $cronjob->setLastRun($now);
                        //Logfile
                        $cronlog[] = 'Cronjob ' . $cronjob->getSearch()->getName() . ' done.';
                    }
                    break;

                case 'daily':
                    $mustRun = $lastRun->add(new \DateInterval('P1D'));

                    if ($mustRun < $now)
                    {
                        //perform cronjob
//                        $this->searchAction($cronjob->getSearch()->getId());
                        $response = $this->forward('sonetrinDefaultBundle:Search:runAjax', array('id' => $cronjob->getSearch()->getId()));
                        //update lastRun
                        $cronjob->setLastRun($now);
                        //Logfile
                        $cronlog[] = 'Cronjob ' . $cronjob->getSearch()->getName() . ' done.';
                    }
                    break;

                case 'monthly':
                    $mustRun = $lastRun->add(new \DateInterval('P1M'));

                    if ($lastRun < $now)
                    {
                        //perform cronjob
//                       $this->searchAction($cronjob->getSearch()->getId());
                        $response = $this->forward('sonetrinDefaultBundle:Search:runAjax', array('id' => $cronjob->getSearch()->getId()));
                        //update lastRun
                        $cronjob->setLastRun($now);
                        //Logfile
                        $cronlog[] = 'Cronjob ' . $cronjob->getSearch()->getName() . ' done.';
                    }

                    break;
            }
        }
        $em->flush();

        if (empty($cronlog))
        {
            $cronlog[] = "No cronjob needed to be run.";
        }
        
        $return = json_encode($cronlog); //jscon encode the array
        return new Response($return, 200, array('Content-Type' => 'application/json')); //make sure it has the correct content type
    }
}
