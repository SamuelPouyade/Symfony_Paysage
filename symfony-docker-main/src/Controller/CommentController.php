<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    #[Route('/comment/new/{articleId}', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function showArticleWithCommentForm(Request $request, Articles $article, EntityManagerInterface $entityManager, $articleId)
    {
        $this->addFlash('success', 'La méthode showArticleWithCommentForm est appelée.');
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setArticle($article);
        dump($article->getId());

        $form = $this->formFactory->create(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump('The form is submitted and valid');

            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        // Affichez la vue qui inclut le formulaire
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'comment_form' => $form->createView(),
            'articleId' => $articleId,
        ]);
    }


}
