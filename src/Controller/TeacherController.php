<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\User;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController
{
    /**
     * @Route("/teachers", name="teacher")
     * @param CourseRepository $courseRepository
     */
    public function index(CourseRepository $courseRepository): Response
    {

       $courses = $courseRepository->findTeachers();

        return $this->render('teacher/index.html.twig', [
            'courses' => $courses,
        ]);
    }
}
