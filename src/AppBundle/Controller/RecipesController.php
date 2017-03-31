<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Recipe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RecipesController extends Controller
{
    /**
     * @Route("/recipes", name="recipesPage")
     */
    public function recipesPageAction(Request $request)
    {

        $argsArray = [
            'name' => 'matt'
        ];
        $templateName = 'recipes';
        return $this->render($templateName. '.html.twig', $argsArray);
    }

    /**
     * @Route("/show/recipe/{title}", name="showOneRecipePage")
     */
    public function recipePageAction($title)
    {

        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository('AppBundle:Recipe')->findOneByTitle($title);

        if (!$recipe) {
            throw $this->createNotFoundException(
                'No recipe found with name: '.$title
            );
        }
        $argsArray = [
            'recipe' => $recipe
        ];

        $templateName = 'recipe/show';
        return $this->render($templateName. '.html.twig', $argsArray);
    }

}