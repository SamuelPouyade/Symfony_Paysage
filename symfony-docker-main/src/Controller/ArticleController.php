<?php

namespace App\Controller;

use App\Entity\Articles;
use DateTime;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Image;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articlesRepository->findAll(),
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
            $imageFile = $form['image']->getData();
        
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
        
                // Créez une nouvelle instance de l'entité Image
                $image = new Image();
                $image->setFilename($newFilename);
        
                // Déplacez le fichier dans le répertoire de destination (public/images)
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/images',
                    $newFilename
                );
        
                // Appelez explicitement persist sur l'entité Image
                $entityManager->persist($image);
        
                // Associez l'instance de l'entité Image à l'article
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
    public function show(Articles $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form['image']->getData();
            
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                
                // Créez une nouvelle instance de l'entité Image
                $image = new Image();
                $image->setFilename($newFilename);
                
                // Déplacez le fichier dans le répertoire de destination (public/images)
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/images',
                    $newFilename
                );
                
                // Appelez explicitement persist sur l'entité Image
                $entityManager->persist($image);
                
                // Associez l'instance de l'entité Image à l'article
                $article->setImage($image);
            }

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
}
