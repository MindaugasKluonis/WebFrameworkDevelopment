<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RecipesController extends Controller
{
    /**
     * @Route("/recipes", name="recipesPage")
     */
    public function loginPageAction(Request $request)
    {
        $argsArray = [
            'name' => 'matt'
        ];
        $templateName = 'recipes';
        return $this->render($templateName. '.html.twig', $argsArray);
    }
}