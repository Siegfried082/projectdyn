<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Course;
use App\Entity\CourseCategory;
use App\Form\CommentType;
use App\Repository\CourseCategoryRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    // Uitilisation du composant ParamConverter
    public function course(Course $course, Request $request, EntityManagerInterface $manager): Response
    {
        // Formulaire de postComment
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setCourse($course);
            $now = new \DateTime('now', new \DateTimeZone('Europe/Brussels'));
            $comment->setCreatedAt($now);
            $manager->persist($comment);
            $manager->flush();
        }

        return $this->render('course/detail.html.twig', [
            'course' => $course,
            'form' => $form->createView()
        ]);
    }
}
