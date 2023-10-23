<?php

namespace App\Controller;

use App\Repository\DepartmentRepository;
use App\Repository\ArticlesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class PublicController extends AbstractController
{
    #[Route('/home', name: 'app_home', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository, DepartmentRepository $departmentRepository, Request $request, PaginatorInterface $paginator, Security $security): Response
    {
        $selectedDepartmentId = $request->query->get('department', null);

        if ($selectedDepartmentId === "") {
            $selectedDepartmentId = null;
        }

        if ($selectedDepartmentId === null) {
            $articles = $articlesRepository->findAll();
        } else {
            $articles = $articlesRepository->findByDepartment($selectedDepartmentId);
        }

        $departments = $departmentRepository->findAll();

        $user = $security->getUser();

        $userArticles = $articlesRepository->findBy(['author' => $user]);

        $pagination = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            2
        );

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
            'departments' => $departments,
            'selectedDepartment' => $selectedDepartmentId,
            'userArticles' => $userArticles,
        ]);
    }
}
