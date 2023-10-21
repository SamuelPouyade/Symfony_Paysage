<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private $formFactory;
    private $security;

    public function __construct(FormFactoryInterface $formFactory, Security $security)
    {
        $this->formFactory = $formFactory;
        $this->security = $security;
    }

    #[Route('/comment/new/{articleId}', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function showArticleWithCommentForm(Request $request, Articles $article, EntityManagerInterface $entityManager, $articleId)
    {
        $comment = new Comment();
        $form = $this->formFactory->create(CommentType::class, $comment);

        $form->handleRequest($request);
        $article = $entityManager->getRepository(Articles::class)->find($articleId);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $comment->setUser($user);
            $comment->setArticle($article);
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->render('article/show.html.twig', [
                'article' => $article,
                'comment_form' => $form->createView(),
                'articleId' => $articleId,
            ]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'comment_form' => $form->createView(),
            'articleId' => $articleId,
        ]);
    }


}
