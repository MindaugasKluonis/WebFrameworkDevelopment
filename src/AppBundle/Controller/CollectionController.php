<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Recipe;
use AppBundle\Entity\RecipeCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Ldap\Adapter\ExtLdap\Collection;

class CollectionController extends Controller
{
    /**
     * @Route("/collection", name="collectionPage")
     */
    public function collectionAction()
    {

        $templateName = 'collection/collection';
        return $this->render($templateName. '.html.twig');

    }

    /**
     * @Route("/collection/create", name="collectionCreatePage")
     */
    public function createCollectionAction(Request $request){

        $templateName = 'collection/createCollection';
        return $this->render($templateName. '.html.twig');


    }

    /**
     * @Route("/collection/createNewCollection", name="processNewCollection")
     */
    public function processCollectionCollectionAction(Request $request){

        if(empty($request->request->get('name'))){
            $this->addFlash(
                'error',
                'collection name cannot be an empty string'
            );
            // forward this to the createAction() method
            return $this->redirect('/collection/create');
        }

        $session = new Session();

        $collection = new RecipeCollection();
        $collection-> setName($request->request->get('name'));
        $collection-> setAuthor($session -> get('username'));
        $collection-> setDescription($request->request->get('description'));


        //entity manager
        $em = $this->getDoctrine()->getManager();

        //tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($collection);

        //actually executes the queries (i.e. the INSERT query)
        $em->flush();

        $this->addFlash(
            'error',
            'created new collection!!'
        );

        return $this -> redirect('/profile');


    }

}