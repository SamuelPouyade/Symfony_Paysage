<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Articles::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('titre'),
            TextField::new('Contenu')
                ->setRequired(true),
            ImageField::new('image')
                ->setBasePath('/images')
                ->setUploadDir('public/images')
                ->setRequired(true),
            BooleanField::new('isValidated', 'Est valide'),
        ];
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
