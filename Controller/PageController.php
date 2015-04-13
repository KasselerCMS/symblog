<?php

namespace Kasseler\Symblog\Controller;

use Kasseler\Symblog\Entity\Enquiry;
use Kasseler\Symblog\Form\EnquiryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PageController extends Controller
{
    public function indexAction()
    {
        $blogs = $this->getDoctrine()->getRepository('KasselerSymblogBundle:Blog')->getLatestBlogs();

        if (!$blogs) {
            $this->createNotFoundException('Blog not found!');
        }

        return $this->render('KasselerSymblogBundle:Page:index.html.twig', array(
            'blogs' => $blogs
        ));
    }

    public function aboutAction()
    {
        return $this->render('KasselerSymblogBundle:Page:about.html.twig');
    }

    public function contactAction(Request $request)
    {
        $enquiry = new Enquiry();
        $form = $this->createForm(new EnquiryType(), $enquiry);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $message = \Swift_Message::newInstance()
                ->setSubject('Contact enquiry from symblog')
                ->setFrom('enquiries@symblog.co.uk')
                ->setTo($this->container->getParameter('symblog.emails.contact_email'))
                ->setBody($this->renderView('KasselerSymblogBundle:Page:contactEmail.txt.twig', array('enquiry' => $enquiry)));
            $this->get('mailer')->send($message);

            $this->get('session')->getFlashBag()->add('blogger-notice', 'Your contact enquiry was successfully sent. Thank you!');

            return $this->redirect($this->generateUrl('kasseler_symblog_contact'));
        }

        return $this->render('KasselerSymblogBundle:Page:contact.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function sidebarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('KasselerSymblogBundle:Blog')->getTags();
        $tagWeights = $em->getRepository('KasselerSymblogBundle:Blog')->getTagWeights($tags);

        $commentLimit = $this->container->getParameter('symblog.comments.latest_comment_limit');
        $latestComments = $em->getRepository('KasselerSymblogBundle:Comment')->getLatestComments($commentLimit);

        return $this->render('KasselerSymblogBundle:Page:sidebar.html.twig', array(
            'latestComments' => $latestComments,
            'tags' => $tagWeights
        ));
    }
}
