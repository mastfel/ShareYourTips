<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArticleController extends AbstractController
{

    /**
     * @Route("/voir-astuces", name="show", methods={"GET"})
     */
    public function show(EntityManagerInterface $entityManager): Response
    {   #  Récupérer les articles ASTUCES non archivés et envoyer les à la vue twig
        $articles = $entityManager->getRepository(Article::class)->findBy(['deletedAt' => null]);

        return $this->render("default/home_astuce.html.twig", [
            'articles' => $articles
        ]);
    }



    /**
     * @Route("/ajouter-une-astuce", name="create_article", methods={"GET|POST"})
     */
    public function createArticle(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        # 1 - Instanciation
        $article = new Article();

        # 2 - Création du formulaire
        $form = $this->createForm(ArticleFormType::class, $article)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $article->setCreatedAt(new DateTime());
            $article->setUpdatedAt(new DateTime());

            # L'alias sera utilisé dans l'url (comme FranceTvInfo) et donc doit être assaini de tout accents et espaces.
            $article->setAlias($slugger->slug($article->getTitle()));

            /** @var UploadedFile $photo */
            $photo = $form->get('photo')->getData();

            # Si une photo a été uploadée dans le formulaire on va faire le traitement nécessaire à son stockage dans notre projet.
            if($photo) {
                # Déconstructioon
                $extension = '.' . $photo->guessExtension();
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
    //                $safeFilename = $article->getAlias();

                # Reconstruction
                $newFilename = $safeFilename . '_' . uniqid() . $extension;

                try {
                    $photo->move($this->getParameter('uploads_dir'), $newFilename);
                    $article->setPhoto($newFilename);
                }
                catch(FileException $exception) {
                    # Code à exécuter en cas d'erreur.
                }
            } # end if($photo)

                # Ajout d'un auteur à l'article (User récupéré depuis la session)
                $article->setAuthor($this->getUser());

                $entityManager->persist($article);
                $entityManager->flush();

                $this->addFlash('success', " Votre astuce est en ligne avec succès !");
                return $this->redirectToRoute('show');

        } # end if ($form)

        # 3 - Création de la vue
        return $this->render("admin/form/article.html.twig", [
            'form' => $form->createView()
        ]);
    } # end function createArticle

    /**
     * @Route("/modifier-un-article_{id}", name="update_article", methods={"GET|POST"})
     */
    public function updateArticle(Article $article, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $originalPhoto = $article->getPhoto();

        # 2 - Création du formulaire
        $form = $this->createForm(ArticleFormType::class, $article, [
            'photo' => $originalPhoto
        ])->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $article->setUpdatedAt(new DateTime());

            # L'alias sera utilisé dans l'url (comme FranceTvInfo) et donc doit être assaini de tout accents et espaces.
            $article->setAlias($slugger->slug($article->getTitle()));

            /** @var UploadedFile $photo */
            $photo = $form->get('photo')->getData();

            # Si une photo a été uploadée dans le formulaire on va faire le traitement nécessaire à son stockage dans notre projet.
            if($photo) {
                # Déconstructioon
                $extension = '.' . $photo->guessExtension();
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
//                $safeFilename = $article->getAlias();

                # Reconstruction
                $newFilename = $safeFilename . '_' . uniqid() . $extension;

                try {
                    $photo->move($this->getParameter('uploads_dir'), $newFilename);
                    $article->setPhoto($newFilename);
                }
                catch(FileException $exception) {
                    # Code à exécuter en cas d'erreur.
                }
            } else {
                $article->setPhoto($originalPhoto);
            } # end if($photo)

            # Ajout d'un auteur à l'article (User récupéré depuis la session)
            $article->setAuthor($this->getUser());

            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('success', "  L'astuce a été modifiée avec succès !");
            return $this->redirectToRoute('show');
        } # end if ($form)

        # 3 - Création de la vue
        return $this->render("admin/form/article.html.twig", [
            'form' => $form->createView(),
            'article' => $article
        ]);
    }# end function updateArticle

    /**
     * @Route("/archiver-un-article_{id}", name="soft_delete_article", methods={"GET"})
     */
    public function softDeleteArticle(Article $article, EntityManagerInterface $entityManager): Response
    {
        $article->setDeletedAt(new DateTime());

        $entityManager->persist($article);
        $entityManager->flush();

        $this->addFlash('success', "L'atuce a bien été archivée");
        return $this->redirectToRoute('show_dashboard');
    }# end function softDelete

    /**
     * @Route("/restaurer-un-article_{id}", name="restore_article", methods={"GET"})
     */
    public function restoreArticle(Article $article, EntityManagerInterface $entityManager): RedirectResponse
    {
        $article->setDeletedAt(null);

        $entityManager->persist($article);
        $entityManager->flush();

        $this->addFlash('success', "L'astuce a bien été restauré");
        return $this->redirectToRoute('show_dashboard');
    }

    /**
     * @Route("/voir-les-articles-archives", name="show_trash", methods={"GET"})
     */
    public function showTrash(EntityManagerInterface $entityManager): Response
    {
        $archivedArticles = $entityManager->getRepository(Article::class)->findByTrash();

        return $this->render("admin/trash/article_trash.html.twig", [
            'archivedArticles' => $archivedArticles
        ]);
    }

    /**
     * @Route("/supprimer-un-article_{id}", name="hard_delete_article", methods={"GET"})
     */
    public function hardDeleteArticle(Article $article, EntityManagerInterface $entityManager): RedirectResponse
    {
        // Suppression manuelle de la photo
        $photo = $article->getPhoto();

        // On utilise la fonction native de PHP unlink() pour supprimer un fichier dans le filesystem
        if($photo) {
            unlink($this->getParameter('uploads_dir'). '/' . $photo);
        }

        $entityManager->remove($article);
        $entityManager->flush();

        $this->addFlash('success', " L'astuce a bien été supprimée avec succès ");
        return $this->redirectToRoute('show_trash');
    }

    /**
     * @Route("/{cat_alias}/{article_alias}_{id}", name="show_article", methods={"GET"})
     */
    public function showArticle(Article $article): Response
    {
        return $this->render("article/show_article.html.twig", [
            'article' => $article
        ]);
    } # end function showArticle()

    /**
     * @Route("/voir-articles/{alias}", name="show_articles_from_category", methods={"GET"})
     */
    public function showArticlesFromCategory(Category $category, EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)
            ->findBy([
                'category' => $category->getId(),
                'deletedAt' => null
            ]);

        return $this->render("article/show_articles_from_category.html.twig", [
            'articles' => $articles,
            'category' => $category
        ]);
    }





    }



























