<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchUserController extends Controller
{
    /**
     * @Route("/searchUser", name="searchUserPage")
     */
    public function searchUserPageAction(Request $request)
    {
        $argsArray = [
            'name' => 'matt'
        ];
        $templateName = 'users';
        return $this->render($templateName. '.html.twig', $argsArray);
    }
}