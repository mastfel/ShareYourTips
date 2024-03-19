<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FooterController extends AbstractController
{
    /**
     * @Route("/footer", name="app_footer")
     */
    public function index(): Response
    {
        return $this->render('footer/index.html.twig', [
            'controller_name' => 'FooterController',
        ]);
    }


 /**
     * @Route("politiques-de-confidentialite", name="app_confidentialite", methods={"GET"})
     *
     * @return Response
     */
    public function apropos(): Response
    {
        return $this->render('footer/politique_de_confidentialite.html.twig');
    }

    /**
     * @Route("mentions-legales", name="app_mentions", methods={"GET"} )
     *
     * @return Response
     */
    public function mentions(): Response
    {
        return $this->render('footer/mentions_legales.html.twig');
    }

    /**
     * @Route("conditions-d-utilisation", name="app_conditions", methods={"GET"} )
     *
     * @return Response
     */
    public function condition(): Response
    {
        return $this->render('footer/conditions.html.twig');
    }



}
