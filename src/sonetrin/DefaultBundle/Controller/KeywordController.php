<?php

namespace sonetrin\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use sonetrin\DefaultBundle\Entity\Keyword;
use sonetrin\DefaultBundle\Form\KeywordType;

/**
 * @Route("/keyword")
 */
class KeywordController extends Controller
{

    /**
     * @Route("/", name="keyword_index")
     * @Template()
     */
    public function indexAction()
    {
        $positiveKeywords = $this->getDoctrine()
                ->getRepository('sonetrinDefaultBundle:Keyword')
                ->findPositiveKeywords();

        $negativeKeywords = $this->getDoctrine()
                ->getRepository('sonetrinDefaultBundle:Keyword')
                ->findNegativeKeywords();

        return array('positiveKeywords' => $positiveKeywords,
            'negativeKeywords' => $negativeKeywords);
    }

    /**
     * @Route("/{id}/delete", name="keyword_delete", requirements={"id" = "\d+"})
     * @Template()
     */
    public function deleteAction(Keyword $id)
    {
        if (false === is_null($id))
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($id);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');
        }
        return $this->redirect($this->generateUrl('keyword_index'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
     * @Route("/add/{assoc}", name="keyword_add", defaults={"assoc" = "positive"})
     * @Template()
     */
    public function addAction($assoc)
    {
        $keyword = new Keyword();
        $form = $this->createForm(new KeywordType($assoc), $keyword);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);

            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($keyword);
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');
                return $this->redirect($this->generateUrl('keyword_index'));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * Displays a form to edit an existing Search entity.
     *
     * @Route("/{id}/edit", name="keyword_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:Keyword')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Keyword entity.');
        }

        $editForm = $this->createForm(new KeywordType($entity->getAssociation()), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Keyword entity.
     *
     * @Route("/{id}/update", name="keyword_update")
     * @Method("post")
     * @Template("sonetrinDefaultBundle:Keyword:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('sonetrinDefaultBundle:Keyword')->find($id);

        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find Keyword entity.');
        }

        $editForm = $this->createForm(new KeywordType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid())
        {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');
            return $this->redirect($this->generateUrl('keyword_index'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Import a keword list
     *
     * @Route("/import", name="keyword_importList")
     * @Template()
     */
    public function importListAction()
    {
        $request = $this->getRequest();
        
        $form = $this->createFormBuilder()
                ->add('keywords', 'textarea', array('label' =>'Keywords (comma seperated)','attr' => array('class' => 'span6', 'rows' => 10)))
                ->add('association', 'choice', 
                        array('expanded' => false,
                                'multiple' => false,
                                'choices' => array('positive' => 'positive',
                                                   'negative' => 'negative')))
                ->getForm();

        if ($request->isMethod('POST'))
        {
            $form->bind($request);

            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
            $keywords = $data['keywords'];
            $em = $this->getDoctrine()->getManager();

            if ($keywords != '')
            {
                $keywordsArray = explode(',', $keywords);

                foreach ($keywordsArray as $word)
                {
                    $word = trim($word);

                    $item_exists = $em->getRepository('sonetrinDefaultBundle:Keyword')->findOneBy(array('english' => $word));

                    if (is_null($item_exists))
                    {
                        $keyword = new Keyword();
                        $keyword->setEnglish($word);
                        $keyword->setAssociation($data['association']);
                        $em->persist($keyword);
                    }
                }

                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');

                return $this->redirect($this->generateUrl('keyword_index'));
            } else
            {
                $this->get('session')->getFlashBag()->add('error', 'The textarea contains some errors!');
            }
        }
        
        return array('form' => $form->createView());
    }

    /**
     * Import a keword list
     *
     * @Route("/import", name="keyword_importList")
     * @Template()
     */
   /* public function importListAction()
    {
        $error = false;

        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        if ($request->getMethod() == 'POST')
        {
            $keywords = $_POST['keywords'];
            $association = $_POST['association'];

            if ($keywords != '')
            {
                $keywordsArray = explode(',', $keywords);


                foreach ($keywordsArray as $word)
                {
                    $word = trim($word);

                    $item_exists = $em->getRepository('sonetrinDefaultBundle:Keyword')->findOneBy(array('english' => $word));

                    if (is_null($item_exists))
                    {
                        $keyword = new Keyword();
                        $keyword->setEnglish($word);
                        $keyword->setAssociation($association);
                        $em->persist($keyword);
                    }
                }

                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'Your changes were saved!');

                return $this->redirect($this->generateUrl('keyword_index'));
            } else
            {
                $this->get('session')->getFlashBag()->add('error', 'The textarea contains some errors!');
            }
        }

        return array(
        );
    }
    */
}
