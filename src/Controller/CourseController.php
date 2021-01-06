<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Course;
use App\Entity\CourseCategory;
use App\Entity\CourseRegister;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\CourseCategoryRepository;
use App\Repository\CourseRegisterRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @param CommentRepository $commentRepository
     * @param CourseRegisterRepository $courseRegisterRepository
     * @param EntityManagerInterface $manager
     * @return Response
     */
    // Uitilisation du composant ParamConverter
    public function course(Course $course, Request $request, EntityManagerInterface $manager, CommentRepository $commentRepository,CourseRegisterRepository $courseRegisterRepository): Response
    {
        $userComment = [];
        $userRegister = [];
        if($this->getUser())
        {
            $userComment = $commentRepository->userCommentCourse($this->getUser()->getId(), $course->getId());
            $userRegister = $courseRegisterRepository->userRegisterCourse($this->getUser()->getId(), $course->getId());
        }

        // Formulaire de postComment
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setCourse($course);
            $now = new \DateTime('now', new \DateTimeZone('Europe/Brussels'));
            $comment->setCreatedAt($now);
            $comment->setIsDisabled(0);
            $comment->setReport(0);

            $manager->persist($comment);
            $manager->flush();
            return $this->redirect($request->getUri());
        }

        return $this->render('course/detail.html.twig', [
            'course' => $course,
            'userComment' => $userComment,
            'userRegister' => $userRegister,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/course/{slug}/register/", name="courseRegister")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param Course $course
     * @param EntityManagerInterface $manager
     * @param CourseRepository $courseRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function courseRegister(CourseRepository $courseRepository, Request $request,EntityManagerInterface $manager, Course $course): Response
    {

        /* @var User $user */
        $user = $this->getUser();
        $courseRegister = new CourseRegister();

        $courseRegister->setUser($user);
        $courseRegister->setCourse($course);
        $courseRegister->setRegistered('0');


        $manager->persist($courseRegister);
        $manager->flush();



        $this->addFlash('success', 'Vous venez de vous inscrire au cours. Celui-ci se trouve dans le panier.');
        return $this->redirectToRoute('profil');
    }


    /**
     * @Route("/report/{id}/", name="reportComment")
     * @IsGranted("ROLE_USER")
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     */
    public function reportComment(Comment $comment, EntityManagerInterface $manager): Response
    {

        $this->addFlash('success', 'Merci de nous avoir signalé ce commentaire.');
        $comment->setReport(1);
        $manager->flush();

        return $this->redirectToRoute('courses');
    }
}
