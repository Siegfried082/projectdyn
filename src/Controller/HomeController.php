<?php

namespace App\Controller;

use App\Repository\CourseRepository;
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
     * @return Response
     */
    public function home(CourseRepository $courseRepository) :Response
    {
        $courses = $courseRepository->findBy(
            [],
            ['createdAt' => 'DESC'],
            3
        );
        return $this->render('home/index.html.twig', ['courses' => $courses]);
    }
}
