<?php

namespace Kasseler\Symblog\Controller;

use Kasseler\Symblog\Entity\Blog;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    public function showAction($id, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Blog $blog */
        $blog = $em->getRepository('KasselerSymblogBundle:Blog')->find($id);

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        $comments = $em->getRepository('KasselerSymblogBundle:Comment')
            ->getCommentsForBlog($blog->getId());

        return $this->render('KasselerSymblogBundle:Blog:show.html.twig', array(
            'blog'      => $blog,
            'comments'  => $comments
        ));
    }
}
