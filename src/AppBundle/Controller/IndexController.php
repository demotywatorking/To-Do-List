<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * Homepage Action
     *
     * @return Response A Response instance
     */
    public function indexAction(): Response
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/setlang/{_locale}", requirements={"_locale" = "en|pl"}, name="setlang")
     *
     * Method sets language on page
     *
     * @return RedirectResponse Redirect to homepage
     */
    public function setLangAction(): RedirectResponse
    {
        $this->addFlash('success', 'all.language');
        return $this->redirectToRoute("todo_all");
    }
}
?>