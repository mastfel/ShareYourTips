<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Article;
use App\Form\RegisterFormType;
use App\Form\UpdateUserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/inscritpion", name="user_register", methods={"GET|POST"})
     */
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        # 1 - Instanciation de class
        $user = new User();

        # 2 - Création du formulaire
        $form = $this->createForm(RegisterFormType::class, $user)
            ->handleRequest($request);

        # 4 - Si le form est soumis ET valide
        if($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new DateTime());
            $user->setUpdatedAt(new DateTime());

            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "Vous  êtes inscrit avec succès !");
            return $this->redirectToRoute('app_login');
        }

        # 3 - On retourne la vue du formulaire
        return $this->render("user/register.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/mon-espace-perso", name="show_profile", methods={"GET"})
     */
    public function showProfile(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findBy(['author' => $this->getUser()]);

        return $this->render("user/show_profile.html.twig", [
            'articles' => $articles
        ]);
    }

    

     /**
      * @Route("/modifier-un-user{id}", name="user_update", methods={"GET|POST"})
     */
    public function update(User $user, Request $request, EntityManagerInterface $entityManager): Response
     {
      $form = $this->createForm(UpdateUserFormType::class, $user)
            ->handleRequest($request);

         if($form->isSubmitted() && $form->isValid()) {
       $entityManager->persist($user);
         $entityManager->flush();

         $this->addFlash('success', " Vos informations ont bien été modifiées ");
           return $this->redirectToRoute('show_profile');
         } // end if()

       return $this->render("user/change_profile.html.twig", [
            'user' => $user,
         'form' => $form->createView()
        ]);
    } # end function update()


    /**
     * @Route("/archiver-un-user/{id}", name="soft_delete_user", methods={"GET"})
     */
    public function softDeleteUser(User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user->setDeletedAt(new DateTime());

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Le membre  a bien été archivé');
        return $this->redirectToRoute('show_dashboard');
    }


/**
     * @Route("/restaurer-un-user/{id}", name="restore_user", methods={"GET"})
     */
    public function restoreUser(User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user->setDeletedAt(null);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Le membre a bien été restauré');
        return $this->redirectToRoute('show_dashboard');
    }


    /**
     * @Route("/supprimer-un-user-{id}", name="hard_delete_user", methods={"GET"})
     */
    public function delete(User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
    $entityManager->remove($user);
    $entityManager->flush();
    
    $this->addFlash('success', " le compte est supprimé avec succès");
    return $this->redirectToRoute("default_home");
    
}


// chow article and update article dans la partie  user 

    /**
     * @Route("/tableau-user", name="show_tableau", methods={"GET"})
     */
    public function showTableau(EntityManagerInterface $entityManager): Response
    {
        

        $articles = $entityManager->getRepository(Article::class)->findBy(['author' => $this->getUser()]);
        
        return $this->render("user/show_dashboard_user.html.twig", [
            'articles' => $articles,
           
        ]);
    }

   













}