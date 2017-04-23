<?php
/**
 * Created by PhpStorm.
 * User: MKluo
 * Date: 21/04/2017
 * Time: 19:24
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SearchController extends Controller
{

    /**
     * @Route("/search", name="search_page")
     */
    public function searchAction(Request $request)
    {


        $argsArray = [


        ];

        $templateName = 'search/index';

        return $this->render($templateName . '.html.twig', $argsArray);


    }


    /**
     * @Route("/search/results", name="search_results")
     */
    public function resultsAction(Request $request)
    {

        $type = $request->request->get('type');
        $query = $request->request->get('query');

        echo $type;

        $data = $this -> getData($type,$query);

        $argsArray = [

            'data' => $data,
            'query' => $query,
            'type' => $type

        ];

        $templateName = 'search/results';

        return $this->render($templateName . '.html.twig', $argsArray);


    }


    public function getData($type,$query){

        $data = null;

        if($type == "User"){

            $data = $this -> getUsers($query);

        }

        elseif($type == "Recipe"){

            $data = $this -> getRecipe($query);

        }

        elseif($type == "Collection"){

            $data = $this -> getCollection($query);

        }

        return $data;

    }

    private function getUsers($query)
    {

        $filtered = array();

        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        if($query != null)
            foreach ($users as $user) {



                if (strpos($user -> getUsername(), $query) !== false) {
                    $filtered[] = $user;
                }

            }

        return $filtered;

    }

    private function getRecipe($query)
    {

        $filtered = array();

        $em = $this->getDoctrine()->getManager();

        $recipes = $em->getRepository('AppBundle:Recipe')->findAll();


        if($query != null)
            foreach ($recipes as $recipe) {



                if (strpos($recipe -> getTitle(), $query) !== false && $recipe -> getPublic() == 'PUBLIC') {
                    $filtered[] = $recipe;
                }

            }

        return $filtered;

    }

    private function getCollection($query)
    {

        $filtered = array();

        $em = $this->getDoctrine()->getManager();

        $collections = $em->getRepository('AppBundle:Collection')->findAll();


        if($query != null)
            foreach ($collections as $collection) {



                if (strpos($collection -> getTitle(), $query) !== false) {
                    $filtered[] = $collection;
                }

            }

        return $filtered;
    }


}