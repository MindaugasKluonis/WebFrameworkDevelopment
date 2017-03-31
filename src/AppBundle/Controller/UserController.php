<?php
/**
 * Created by PhpStorm.
 * User: MKluo
 * Date: 31/03/2017
 * Time: 11:51
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Recipe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{

    /**
     * @Route("/profile", name="profilePage")
     */
    public function profileAction()
    {


        $session = new Session();

        if(!$session -> get('username')){


            $this->addFlash(
                'error',
                'log in first to access your profile'
            );

            return $this->redirect('/');

        }

        $templateName = 'users/profile';
        return $this->render($templateName. '.html.twig');
    }

    /**
     * @Route("/recipe/processNewRecipe", name="recipesCreateRecipe")
     */
    public function createRecipeAction(Request $request){


        if(empty($request->request->get('title'))){
            $this->addFlash(
                'error',
                'student name cannot be an empty string'
            );
            // forward this to the createAction() method
            return $this->redirect('/recipes/create/new');
        }

        $session = new Session();

        $recipe = new Recipe();
        $recipe-> setTitle($request->request->get('title'));
        $recipe-> setAuthor($session -> get('username'));
        $recipe-> setIngredients($request->request->get('ingredients'));
        $recipe-> setSteps($request->request->get('steps'));
        $recipe-> setSummary($request->request->get('summary'));


        //entity manager
        $em = $this->getDoctrine()->getManager();

        //tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($recipe);

        //actually executes the queries (i.e. the INSERT query)
        $em->flush();

        $this->addFlash(
            'error',
            'created new recipe!!'
        );

        return $this -> redirect('/profile');

    }

    /**
     * @Route("/recipes/create/new", name="recipesCreateNewPage")
     */
    public function createNewRecipeAction(Request $request){

        $templateName = 'recipe/recipeCreation';
        return $this->render($templateName. '.html.twig');

    }

    /**
     * @Route("/profile/edit", name="editPage")
     */
    public function showEditableRecipesAction()
    {

        $session = new Session();

        $recipeRepository = $repository = $this->getDoctrine()->getRepository('AppBundle:Recipe');

        $recipes = $repository ->findByAuthor($session -> get('username'));

        $argsArray = [
            'recipes' => $recipes
        ];

        $templateName = 'recipe/showEditable';
        return $this->render($templateName. '.html.twig',$argsArray);
    }

    /**
     * @Route("/edit/recipe/edit/{id}", name="editRecipePage")
     */
    public function editRecipeAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $recipes = $em->getRepository('AppBundle:Recipe')->findById($id);

        if (!$recipes) {
            throw $this->createNotFoundException(
                'No recipe found with name: '.$id
            );
        }
        $argsArray = [
            'recipes' => $recipes
        ];

        $templateName = 'recipe/editRecipe';
        return $this->render($templateName. '.html.twig',$argsArray);
    }


    /**
     * @Route("/recipe/edit/new", name="editNewRecipePage")
     */
    public function editNewRecipeAction(Request $request)
    {

        if(empty($request->request->get('title'))){
            $this->addFlash(
                'error',
                'student name cannot be an empty string'
            );
            // forward this to the createAction() method
            return $this->redirect('/profile');
        }

        $session = new Session();

        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository('AppBundle:Recipe')-> find($request->request->get('id'));

        $recipe-> setTitle($request->request->get('title'));
        $recipe-> setAuthor($session -> get('username'));
        $recipe-> setIngredients($request->request->get('ingredients'));
        $recipe-> setSteps($request->request->get('steps'));
        $recipe-> setSummary($request->request->get('summary'));


//      tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($recipe);

        //actually executes the queries (i.e. the INSERT query)
        $em->flush();

        $this->addFlash(
            'error',
            'edited new recipe!!'
        );

        $templateName = '/profile';
        return $this->redirect($templateName);
    }

}