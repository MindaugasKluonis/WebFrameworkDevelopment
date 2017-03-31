<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="loginPage")
     */
    public function loginPageAction(Request $request)
    {
        $argsArray = [

        ];
        $templateName = 'login';
        return $this->render($templateName. '.html.twig', $argsArray);
    }

    /**
     * @Route("/login/processNewLogin", name="loginProcessPage")
     */
    public function loginProcessingAction(Request $request){

        $flash = false;

        if(empty($request->request->get('username'))){
            $this->addFlash(
                'error',
                'username cannot be an empty string'
            );

            $flash = true;

        }

        if(empty($request->request->get('password'))){

            $this->addFlash(
                'error',
                'password cannot be an empty string'
            );

            $flash = true;
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneByUsername($request->request->get('username'));

        if (!empty($user)){

            if($user -> getPassword() != $request->request->get('password')){

                $this->addFlash(
                    'error',
                    'wrong username or password'
                );

                $flash = true;
            }

        }

        if($flash){

            return $this->loginPageAction($request);

        }

        $session = new Session();
        $session ->set('username', $user -> getUsername());
        $session ->set('role', $user -> getRole());



        return $this -> profileAction();

    }

    public function profileAction(){

        $templateName = 'users/profile';
        return $this->redirect('/profile');

    }

}