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

        $session = new Session();

        $em = $this->getDoctrine()->getManager();
        $collection = $em->getRepository('AppBundle:RecipeCollection')->findByAuthor($session -> get('username'));

        $argsArray = [
            'collections' => $collection
        ];


        $templateName = 'collection/collection';
        return $this->render($templateName. '.html.twig',$argsArray);

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

        return $this -> redirect('/collection');


    }

    /**
     * @Route("/collection/delete/{name}", name="deleteCollection")
     */
    public function deleteCollectionAction($name)
    {

           $em = $this -> getDoctrine()-> getManager();
           $collection = $em->getRepository('AppBundle:RecipeCollection')->findOneByName($name);

           $recipes = $em-> getRepository('AppBundle:Recipe')->findByCollection($name);


           foreach ($recipes as $recipe) {

                $recipe -> setCollection("");
                $em->persist($recipe);

           }


           $em->remove($collection);
           $em->flush();

           return $this->redirect('/collection');
    }

    /**
     * @Route("/collection/edit/{name}", name="editCollection")
     */
    public function editCollectionAction($name)
    {

        $em = $this -> getDoctrine()-> getManager();
        $collection = $em->getRepository('AppBundle:RecipeCollection')->findOneByName($name);

        $argsArray = [
            'collection' => $collection
        ];

        $templateName = 'collection/editCollection';
        return $this->render($templateName. '.html.twig',$argsArray);
    }

    /**
     * @Route("/collection/editCollection", name="processEditCollection")
     */
    public function editProcessAction(Request $request)
    {

        $em = $this -> getDoctrine()-> getManager();
        $collection = $em->getRepository('AppBundle:RecipeCollection')->find($request->request->get('id'));

        $collection -> setName($request->request->get('name'));
        $collection -> setDescription($request->get('description'));

        $em -> persist($collection);
        $em -> flush();

        $this->addFlash(
            'error',
            'edited collection!!'
        );

        $templateName = 'collection/collection';
        return $this->collectionAction();
    }

}