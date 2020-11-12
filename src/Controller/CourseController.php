<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\CourseCategory;
use App\Repository\CourseCategoryRepository;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CourseController
 */
class CourseController extends AbstractController
{
    /**
     * @Route("/courses", name="courses")
     * @param CourseCategoryRepository $repoCategory
     * @param CourseRepository $repoCourse
     * @return Response
     */
    public function courses(CourseCategoryRepository $repoCategory, CourseRepository $repoCourse): Response
    {
        $courses = $repoCourse->findBy(
            ['isPublished' => true],
            ['name' => 'ASC']
        );
        $categories = $repoCategory->findBy(
            [],
            ['name' => 'ASC']
        );
        return $this->render('course/courses.html.twig', [
            'courses' => $courses,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/course/{slug}", name="course")
     * @param Course $course
     * @return Response
     */
    // Uitilisation du composant ParamConverter
    public function course(Course $course): Response
    {
        return $this->render('course/detail.html.twig', ['course' => $course]);
    }
}
