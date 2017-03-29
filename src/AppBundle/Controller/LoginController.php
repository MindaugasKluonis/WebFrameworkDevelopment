<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="loginPage")
     */
    public function loginPageAction(Request $request)
    {
        $argsArray = [
            'name' => 'matt'
        ];
        $templateName = 'login';
        return $this->render($templateName. '.html.twig', $argsArray);
    }
}