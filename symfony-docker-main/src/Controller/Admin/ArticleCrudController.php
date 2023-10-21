<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Articles::class;
    }

    public function validateArticle(): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $articleId = $this->request->query->get('entityId');

        $article = $entityManager->getRepository(Articles::class)->find($articleId);

        if (!$article) {
            $this->addFlash('error', 'L\'article n\'existe pas.');
        } else {
            $article->setIsValidated(true);

            $entityManager->flush();

            $this->addFlash('success', 'L\'article a été validé avec succès.');
        }

        $adminUrlGenerator = $this->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ArticleCrudController::class)->generateUrl());
    }
}
