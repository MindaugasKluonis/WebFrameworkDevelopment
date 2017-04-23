<?php
/**
 * Created by PhpStorm.
 * User: MKluo
 * Date: 22/04/2017
 * Time: 23:15
 */


namespace AppBundle\Controller;

use AppBundle\Entity\Comments;
use AppBundle\Entity\Recipe;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


class CommentController extends Controller{

    /**
     * Lists all collection entities.
     *
     * @Route("/recipe/show/view/comment/{id}", name="comment_recipe")
     */
    public function commentAction(Request $request, Recipe $recipe)
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $comment = new Comments();

        $user = $em -> getRepository('AppBundle:User')->findOneByUsername(
            $this->get('security.token_storage')->getToken()->getUser()->getUsername()
        );

        $comment -> setContent($request ->request->get('comment'));
        $comment -> setAuthor($user);

        $recipe = $em->getRepository('AppBundle:Recipe')->find($recipe);
        $recipe -> addComment($comment);

        $em -> persist($comment);
        $em -> flush();


        return $this->redirectToRoute('view_recipe', array('id' => $recipe -> getId()));
    }

    private function getContext()
    {
    }

}