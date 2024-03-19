<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class AdminController extends AbstractController
{
    /**
     * @Route("/tableau-de-bord", name="show_dashboard", methods={"GET"})
     */
    public function showDashboard(EntityManagerInterface $entityManager): Response
    {
        # 2ème façon de bloquer un accès à un user en fonction de son rôle
        # (la première se trouve dans "access control" -> config/packages/security.yaml)
        // Ce bloc de code nous permet de vérifier si le rôle du user est ADMIN, sinon cela lance une
        // une erreur, qui est attrapée dans le catch et cela redirige avec un message dans une partie
        // autorisée pour les différents rôles.
        try {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        } catch (AccessDeniedException $exception) {
            $this->addFlash('warning', 'Cette partie du site est réservée aux admins');
            return $this->redirectToRoute('default_home');
        }

        $articles = $entityManager->getRepository(Article::class)->findBy(['deletedAt' => null]);
        $categories = $entityManager->getRepository(Category::class)->findAll();
        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render("admin/show_dashboard.html.twig", [
            'articles' => $articles,
            'categories' => $categories,
            'user'=> $users
        ]);
    }

   


} # end class
