<?php
/**
 * Created by PhpStorm.
 * User: MKluo
 * Date: 30/03/2017
 * Time: 18:00
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class LogoutController extends Controller
{

    /**
     * @Route("/logout", name="logoutPage")
     */
    public function logoutAction(Request $request)
    {

        $templateName = 'users/logout';
        return $this->render($templateName. '.html.twig');


    }


    /**
     * @Route("/logout/confirm", name="logoutConfirmPage")
     */
    public function confirmLogoutAction(Request $request){

        $session = new Session();

        $session -> clear();

        return $this->redirect('/');


    }

}