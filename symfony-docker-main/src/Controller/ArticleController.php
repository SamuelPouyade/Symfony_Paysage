<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Comment;
use App\Form\ArticlesType;
use App\Form\CommentType;
use App\Repository\DepartmentRepository;
use DateTime;
use App\Repository\ArticlesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Image;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository, DepartmentRepository $departmentRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $selectedDepartmentId = $request->query->get('department', null);

        $articles = $articlesRepository->findByDepartment($selectedDepartmentId);
        $departments = $departmentRepository->findAll();

        $pagination = $paginator->paginate(
            $articles,  // Utilisez le résultat de la recherche directement ici
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
            'departments' => $departments,
            'selectedDepartment' => $selectedDepartmentId,
        ]);
    }



    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Articles();
        $article->setDatePublication(new DateTime());

        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form['image']->getData(); // Notez le changement de nom ici

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                $image = new Image();
                $image->setFilename($newFilename);

                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/images',
                    $newFilename
                );

                $entityManager->persist($image);

                $article->setImage($image);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Request $request, Articles $article): Response
    {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'comment_form' => $commentForm->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        $existingImage = $article->getImage();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);
         // Image existante

        if ($form->isSubmitted() && $form->isValid()) {
            $newImageFile = $form['image']->getData(); // Nouveau fichier

            if ($newImageFile !== null) {
                // Un nouveau fichier a été téléchargé, traitez-le
                $newFilename = uniqid().'.'.$newImageFile->guessExtension();
                $newImage = new Image();
                $newImage->setFilename($newFilename);

                $newImageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/images',
                    $newFilename
                );

                $entityManager->persist($newImage);
                $article->setImage($newImage); // Définir le champ image sur la nouvelle image
            } else {
                // Aucun nouveau fichier n'a été téléchargé, conservez l'image existante
                $article->setImage($existingImage); // Rétablir l'image existante
            }

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }





    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }

    public function validateArticle(Articles $article, Security $security, FlashBagInterface $flashBag): JsonResponse
    {
        $user = $security->getUser();

        // Vérifiez si l'utilisateur actuel est administrateur ou a les autorisations nécessaires pour valider l'article.
        // Vous pouvez définir votre propre logique de vérification des autorisations.

        if ($user->isAdmin() && !$article->isValidated()) {
            $article->setIsValidated(true);

            // Enregistrez les modifications
            $this->getDoctrine()->getManager()->flush();

            // Ajoutez un message flash pour informer de la validation de l'article.
            $flashBag->add('success', 'L\'article a été validé avec succès.');

            return new JsonResponse(['message' => 'Article validé avec succès.']);
        } else {
            // Gérez le cas où l'utilisateur n'a pas les autorisations requises pour valider l'article.
            return new JsonResponse(['message' => 'Vous n\'avez pas l\'autorisation de valider cet article.'], 403);
        }
    }
}
