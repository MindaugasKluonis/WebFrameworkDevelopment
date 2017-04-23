<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Tag controller.
 *
 * @Route("tag")
 */
class TagController extends Controller
{
    /**
     * Lists all tag entities.
     *
     * @Route("/", name="tag_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tags = null;

        $user = new User();

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){

            $tags = $em->getRepository('AppBundle:Tag')->findAll();

        }

        elseif (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {

            $user = $em->getRepository('AppBundle:User')->findOneByUsername(
                $this->get('security.token_storage')->getToken()->getUser()->getUsername()
            );

            $tags = $em->getRepository('AppBundle:Tag')->findByAuthor($user);
        }



        return $this->render('tag/index.html.twig', array(
            'tags' => $tags,
        ));
    }

    /**
     * Creates a new tag entity.
     *
     * @Route("/new", name="tag_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $tag = new Tag();
        $form = $this->createForm('AppBundle\Form\newTagType', $tag);
        $form->handleRequest($request);

        $user = new User();

        $em = $this->getDoctrine()->getManager();

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {

            $user = $em->getRepository('AppBundle:User')->findOneByUsername(
                $this->get('security.token_storage')->getToken()->getUser()->getUsername()
            );

            $tag -> setAuthor($user);
        }


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();

            $this->addFlash(
                'error',
                'Thank you for proposing new tag'
            );

            return $this->redirectToRoute('proposed_tag');
        }

        return $this->render('tag/new.html.twig', array(
            'tag' => $tag,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a tag entity.
     *
     * @Route("/{id}", name="tag_show")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function showAction(Tag $tag)
    {
        $deleteForm = $this->createDeleteForm($tag);

        return $this->render('tag/show.html.twig', array(
            'tag' => $tag,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing tag entity.
     *
     * @Route("/{id}/edit", name="tag_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Tag $tag)
    {
        $deleteForm = $this->createDeleteForm($tag);
        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){

            $editForm = $this->createForm('AppBundle\Form\TagType', $tag);

        }

        else{

            $editForm = $this->createForm('AppBundle\Form\newTagType', $tag);

        }

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'error',
                'Edited tag'
            );

            $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findOneByUsername(
                $this->get('security.token_storage')->getToken()->getUser()->getUsername()
            );

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render('tag/edit.html.twig', array(
            'tag' => $tag,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a tag entity.
     *
     * @Route("/{id}", name="tag_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Tag $tag)
    {
        $form = $this->createDeleteForm($tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tag);
            $em->flush();

            $this->addFlash(
                'error',
                'Deleted tag'
            );

            $user = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findOneByUsername(
                $this->get('security.token_storage')->getToken()->getUser()->getUsername()
            );

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->redirectToRoute('tag_index');
    }

    /**
     * Creates a form to delete a tag entity.
     *
     * @param Tag $tag The tag entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Tag $tag)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tag_delete', array('id' => $tag->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Lists all tag entities.
     *
     * @Route("/view/proposed", name="proposed_tag")
     * @Method("GET")
     */
    public function proposedAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tags = $em->getRepository('AppBundle:Tag')->findAll();

        return $this->render('tag/proposed.html.twig', array(
            'tags' => $tags,
        ));
    }

    /**
     * Lists all tag entities.
     *
     * @Route("/view/proposed/freeze/{id}", name="freeze_tag")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function reportTagAction(Tag $tag)
    {
        $em = $this->getDoctrine()->getManager();

        $tag = $em->getRepository('AppBundle:Tag')->find($tag);

        $tag -> setStatus("Frozen");

        $em->persist($tag);
        $em->flush();

        $this->addFlash(
            'error',
            'Tag was reported'
        );

        return $this->redirectToRoute('proposed_tag');
    }

    /**
     * Lists all tag entities.
     *
     * @Route("/view/proposed/vote/{id}", name="vote_tag")
     * @Method("GET")
     */
    public function voteTagAction(Tag $tag)
    {
        $em = $this->getDoctrine()->getManager();

        $tag = $em->getRepository('AppBundle:Tag')->find($tag);

        if (TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER')){

            $tag -> setVotes($tag->getVotes() + 5);

        }

        else{

            $tag -> setVotes($tag->getVotes() + 1);

        }


        $em->persist($tag);
        $em->flush();

        $this->addFlash(
            'error',
            'Thank you for your vote'
        );

        return $this->redirectToRoute('proposed_tag');
    }
}
