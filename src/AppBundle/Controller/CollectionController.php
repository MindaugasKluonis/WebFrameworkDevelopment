<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Collection;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Collection controller.
 *
 * @Route("collection")
 */
class CollectionController extends Controller
{
    /**
     * Lists all collection entities.
     *
     * @Route("/", name="collection_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User();

        $user = $em -> getRepository('AppBundle:User')->findOneByUsername(
            $this->get('security.token_storage')->getToken()->getUser()->getUsername()
        );

        $collections = $em->getRepository('AppBundle:Collection')->findByAuthor($user);
        $sharedCollections = $user -> getSharedCollections();

        return $this->render('collection/index.html.twig', array(
            'collections' => $collections,
            'shared_collections' => $sharedCollections
        ));
    }

    /**
     * Creates a new collection entity.
     *
     * @Route("/new", name="collection_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $collection = new Collection();
        $form = $this->createForm('AppBundle\Form\CollectionType', $collection);
        $form->handleRequest($request);

        $user = new User();

        $em = $this->getDoctrine()->getManager();
        $user = $em -> getRepository('AppBundle:User')->findOneByUsername(
            $this->get('security.token_storage')->getToken()->getUser()->getUsername()
        );

        $collection -> setAuthor($user);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($collection);
            $em->flush();

            return $this->redirectToRoute('collection_show', array('id' => $collection->getId()));
        }

        return $this->render('collection/new.html.twig', array(
            'collection' => $collection,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a collection entity.
     *
     * @Route("/{id}", name="collection_show")
     * @Method("GET")
     */
    public function showAction(Collection $collection)
    {
        $deleteForm = $this->createDeleteForm($collection);

        return $this->render('collection/show.html.twig', array(
            'collection' => $collection,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing collection entity.
     *
     * @Route("/{id}/edit", name="collection_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Collection $collection)
    {
        $deleteForm = $this->createDeleteForm($collection);
        $editForm = $this->createForm('AppBundle\Form\CollectionType', $collection);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('collection_edit', array('id' => $collection->getId()));
        }

        return $this->render('collection/edit.html.twig', array(
            'collection' => $collection,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a collection entity.
     *
     * @Route("/{id}", name="collection_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Collection $collection)
    {
        $form = $this->createDeleteForm($collection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($collection);
            $em->flush();
        }

        return $this->redirectToRoute('collection_index');
    }

    /**
     * Creates a form to delete a collection entity.
     *
     * @param Collection $collection The collection entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Collection $collection)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('collection_delete', array('id' => $collection->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Displays a form to edit an existing recipe entity.
     *
     * @Route("/show/all", name="public_collections")
     *
     */
    public function showPublicCollectionsAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();


        $collections = $em->getRepository('AppBundle:Collection')->findAll();

        $collections = $this -> getPublicCollections($collections);


        return $this->render('collection/publicCollections.html.twig', array(
            'collections' => $collections
        ));

    }

    public function getPublicCollections($collections){

        $filtered = array();

        foreach ($collections as $value) {
            if($value -> getPublic() == 'PUBLIC'){

                $filtered[] = $value;

            }
        }

        return $filtered;


    }

    /**
     * Displays a form to edit an existing recipe entity.
     *
     * @Route("/show/view/{id}", name="view_collection")
     *
     */
    public function viewPublicRecipesAction(Request $request, Collection $collection)
    {

        $showOptions = true;

        $user = new User();

        $em = $this->getDoctrine()->getManager();

        $user = $em -> getRepository('AppBundle:User')->findOneByUsername(
            $this->get('security.token_storage')->getToken()->getUser()->getUsername()
        );

        $collection_check = $user -> getSharedCollections();

        foreach ($collection_check as $value){

            if($value == $collection){

                $showOptions = false;

            }

        }


        return $this->render('collection/view.html.twig', array(
            'options' => $showOptions,
            'collection' => $collection,
        ));

    }

    /**
     * Displays a form to edit an existing recipe entity.
     *
     * @Route("/show/view/{id}/share_collection", name="share_collection")
     *
     */
    public function sharePublicCollectionAction(Request $request, Collection $collection)
    {

        $user = new User();

        $em = $this->getDoctrine()->getManager();

        $user = $em -> getRepository('AppBundle:User')->findOneByUsername(
            $this->get('security.token_storage')->getToken()->getUser()->getUsername()
        );

        $sharedCollection = $em -> getRepository('AppBundle:Collection')->find($collection);

        $user -> addSharedCollection($sharedCollection);

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('public_collections');

    }

    /**
     * Displays a form to edit an existing recipe entity.
     *
     * @Route("/collection/remove/shared/{id}", name="remove_shared_collection")
     *
     */
    public function removeSharedCollectionAction(Request $request, Collection $collection)
    {

        $user = new User();

        $em = $this->getDoctrine()->getManager();

        $user = $em -> getRepository('AppBundle:User')->findOneByUsername(
            $this->get('security.token_storage')->getToken()->getUser()->getUsername()
        );

        $user -> removeSharedCollection($collection);

        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('collection_index');

    }


}
