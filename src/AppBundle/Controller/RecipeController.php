<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Recipe;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Recipe controller.
 *
 * @Route("recipe")
 */
class RecipeController extends Controller
{
    /**
     * Lists all recipe entities.
     *
     * @Route("/", name="recipe_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User();

        $user = $em -> getRepository('AppBundle:User')->findOneByUsername(
            $this->get('security.token_storage')->getToken()->getUser()->getUsername()
        );

        $recipes = $em->getRepository('AppBundle:Recipe')->findByAuthor($user);
        $sharedRecipes = $user -> getSharedRecipes();

        return $this->render('recipe/index.html.twig', array(
            'recipes' => $recipes,
            'shared_recipes' => $sharedRecipes
        ));
    }

    /**
     * Creates a new recipe entity.
     *
     * @Route("/new", name="recipe_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function newAction(Request $request)
    {
        $recipe = new Recipe();
        $user = new User();

        $em = $this->getDoctrine()->getManager();
        $user = $em -> getRepository('AppBundle:User')->findOneByUsername(
            $this->get('security.token_storage')->getToken()->getUser()->getUsername()
        );

        $form = $this->createForm('AppBundle\Form\RecipeType', $recipe, array(

            'user_id' => $user->getId(),
            'public' => $recipe->getPublic()

        ));

        $form->handleRequest($request);

        $recipe -> setAuthor($user);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();

            $this->addFlash(
                'error',
                'New recipe created.'
            );

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render('recipe/new.html.twig', array(
            'recipe' => $recipe,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a recipe entity.
     *
     * @Route("/{id}", name="recipe_show")
     * @Method("GET")
     *
     */
    public function showAction(Recipe $recipe)
    {
        $deleteForm = $this->createDeleteForm($recipe);

        return $this->render('recipe/show.html.twig', array(
            'recipe' => $recipe,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing recipe entity.
     *
     * @Route("/{id}/edit", name="recipe_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request, Recipe $recipe)
    {
        $deleteForm = $this->createDeleteForm($recipe);
        $user = new User();

        $em = $this->getDoctrine()->getManager();
        $user = $em -> getRepository('AppBundle:User')->findOneByUsername(
            $this->get('security.token_storage')->getToken()->getUser()->getUsername()
        );

        $editForm = $this->createForm('AppBundle\Form\RecipeType', $recipe, array(

            'user_id' => $user->getId(),
            'public' => $recipe->getPublic()

        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

                $this->addFlash(
                    'error',
                    'Edit finished'
                );

                return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render('recipe/edit.html.twig', array(
            'recipe' => $recipe,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a recipe entity.
     *
     * @Route("/{id}", name="recipe_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction(Request $request, Recipe $recipe)
    {
        $form = $this->createDeleteForm($recipe);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $user = $em -> getRepository('AppBundle:User')->findOneByUsername(
            $this->get('security.token_storage')->getToken()->getUser()->getUsername()
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($recipe);
            $em->flush();
        }

        $this->addFlash(
            'error',
            'Deleted recipe'
        );

        return $this->redirectToRoute('user_show', array('id' => $user->getId()));
    }

    /**
     * Creates a form to delete a recipe entity.
     *
     * @param Recipe $recipe The recipe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Recipe $recipe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('recipe_delete', array('id' => $recipe->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Displays a form to edit an existing recipe entity.
     *
     * @Route("/show/all", name="public_recipes")
     *
     */
    public function showPublicRecipesAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();


        $recipes = $em->getRepository('AppBundle:Recipe')->findAll();

        $recipes = $this -> getPublicRecipes($recipes);


        return $this->render('recipe/publicRecipes.html.twig', array(
            'recipes' => $recipes,
        ));

    }

    public function getPublicRecipes($recipes){

        $filtered = array();

        foreach ($recipes as $value) {
            if($value -> getPublic() == 'PUBLIC'){

                $filtered[] = $value;

            }
        }

        return $filtered;


    }

    /**
     * Displays a form to edit an existing recipe entity.
     *
     * @Route("/show/view/{id}", name="view_recipe")
     *
     */
    public function viewPublicRecipesAction(Request $request, Recipe $recipe)
    {

        $showOptions = true;

        $user = new User();

        $em = $this->getDoctrine()->getManager();

        if($this->get('security.token_storage')->getToken()->getUser() == null) {
            $user = $em->getRepository('AppBundle:User')->findOneByUsername(
                $this->get('security.token_storage')->getToken()->getUser()->getUsername()
            );

            $recipe_check = $user->getSharedRecipes();

            foreach ($recipe_check as $value) {

                if ($value == $recipe) {

                    $showOptions = false;

                }

            }


            return $this->render('recipe/view.html.twig', array(
                'options' => $showOptions,
                'recipe' => $recipe,
            ));

        }

        else {

            return $this->render('recipe/view.html.twig', array(
                'options' => false,
                'recipe' => $recipe,
            ));

        }

    }


    /**
     * Displays a form to edit an existing recipe entity.
     *
     * @Route("/show/view/{id}/share_recipe", name="share_recipe")
     * @Security("has_role('ROLE_USER')")
     *
     */
    public function sharePublicRecipesAction(Request $request, Recipe $recipe)
    {

        $user = new User();

        $em = $this->getDoctrine()->getManager();

        $user = $em -> getRepository('AppBundle:User')->findOneByUsername(
            $this->get('security.token_storage')->getToken()->getUser()->getUsername()
        );

        $sharedRecipe = $em -> getRepository('AppBundle:Recipe')->find($recipe);

        $user -> addSharedRecipe($sharedRecipe);

        $em->persist($user);
        $em->flush();

        $this->addFlash(
            'error',
            'Saved shared recipe'
        );

        return $this->redirectToRoute('user_show', array('id' => $user->getId()));

    }

    /**
     * Displays a form to edit an existing recipe entity.
     *
     * @Route("/recipe/remove/shared/{id}", name="remove_shared_recipe")
     * @Security("has_role('ROLE_USER')")
     *
     */
    public function removeSharedRecipesAction(Request $request, Recipe $recipe)
    {

        $user = new User();

        $em = $this->getDoctrine()->getManager();

        $user = $em -> getRepository('AppBundle:User')->findOneByUsername(
            $this->get('security.token_storage')->getToken()->getUser()->getUsername()
        );

        $user -> removeSharedRecipe($recipe);

        $em->persist($user);
        $em->flush();

        $this->addFlash(
            'error',
            'Removed shared recipe'
        );

        return $this->redirectToRoute('user_show', array('id' => $user->getId()));

    }



}
