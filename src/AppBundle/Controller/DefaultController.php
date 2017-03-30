<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Recipe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {

        $recipeRepository = $repository = $this->getDoctrine()->getRepository('AppBundle:Recipe');

        $recipes = $repository ->findAll();

        $argsArray = [
            'recipes' => $recipes
        ];
        $templateName = 'index';
        return $this->render($templateName. '.html.twig', $argsArray);
    }

}
