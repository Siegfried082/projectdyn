<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param CourseRepository $courseRepository
     * @param NewsRepository $newsRepository
     * @return Response
     */
    public function home(CourseRepository $courseRepository,NewsRepository $newsRepository) :Response
    {
        $courses = $courseRepository->findBy(
            [],
            ['createdAt' => 'DESC'],
            3
        );

        $news = $newsRepository->findBy(
            [],
            ['createdAt' => 'DESC'],
            4
        );

        return $this->render('home/index.html.twig', [
            'courses' => $courses,
            'news' => $news
        ]);
    }
}
