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


        $user = $this->get('security.token_storage')->getToken()->getUser();

        $argsArray = [

            'user' => $user,

        ];
        $templateName = 'index';
        return $this->render($templateName. '.html.twig', $argsArray);
    }

}
