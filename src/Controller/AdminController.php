<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\CourseRepository;
use App\Repository\NewsRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/acp", name="admin")
     * @param CourseRepository $repositoryCourses
     * @param UserRepository $repositoryUsers
     * @param CommentRepository $repositoryComments
     * @param NewsRepository $repositoryNews
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(NewsRepository $repositoryNews,CourseRepository $repositoryCourses,UserRepository $repositoryUsers,CommentRepository $repositoryComments)
    {

        $totalCourses = count($repositoryCourses->findAll());
        $totalUsers = count($repositoryUsers->findAll());
        $totalComments = count($repositoryComments->findAll());
        $totalNews = count($repositoryNews->findAll());

        return $this->render('admin/index.html.twig', [
            'totalCourses' => $totalCourses,
            'totalUsers' => $totalUsers,
            'totalComments' => $totalComments,
            'totalNews' => $totalNews,
        ]);
    }
}
