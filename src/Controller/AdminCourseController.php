<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCourseController extends AbstractController
{
    /**
     * @Route("/acp/courses", name="acp_courses")
     * @param CourseRepository $repositoryCourses
     * @IsGranted("ROLE_ADMIN")
     */
    public function courses(CourseRepository $repositoryCourses): Response
    {
        $courses = $repositoryCourses->findAll();
        return $this->render('admin/courses.html.twig', [
            'courses' => $courses
        ]);
    }

    /**
     * @Route("/acp/course/{id}/", name="showCourse")
     * @param int $id
     * @param CourseRepository $repositoryCourses
     * @IsGranted("ROLE_ADMIN")
     */
    public function showCourse(CourseRepository $repositoryCourses, int $id): Response
    {



        $course = $repositoryCourses->find($id);
        $coursesRegisters =  $course->getCourseRegisters();

        return $this->render('admin/showCourse.html.twig', [
            'course' => $course,
            'coursesRegisters' => $coursesRegisters
        ]);
    }


    /**
     * @Route("/acp/course/{id}/edit", name="editCourses")
     * @param Request $request
     * @param Course $course
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_ADMIN")
     */
    public function editCourses(Course $course,Request $request, EntityManagerInterface $manager): Response
    {



        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            /** @var UploadedFile $programFile */
            $programFile = $form->get('program')->getData();

            if ($programFile) {
                $newFilename = uniqid() . '.' . $programFile->guessExtension();

                try {
                    $programFile->move(
                        $this->getParameter('program_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $course->setProgram($newFilename);
            }


            /** @var UploadedFile $avatarFile */
            $avatarFile = $form->get('image')->getData();

            if ($avatarFile) {
                $newFilename = uniqid() . '.' . $avatarFile->guessExtension();

                try {
                    $avatarFile->move(
                        $this->getParameter('course_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $course->setImage($newFilename);
            }

            $manager->flush();
            $this->addFlash('success', 'Le cours a été édité avec succès');
            return $this->redirectToRoute('acp_courses');
        }
        return $this->render('admin/editCourses.html.twig',['form' => $form->createView()]);
    }


    /**
     * @Route("/acp/courses/add", name="acp_courses_add")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_ADMIN")

     */
    public function addCourse(Request $request, EntityManagerInterface $manager): Response
    {
        $course = new Course();
        $slugify = new Slugify();

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $programFile */
            $programFile = $form->get('program')->getData();

            if ($programFile) {
                $newFilename = uniqid() . '.' . $programFile->guessExtension();

                try {
                    $programFile->move(
                        $this->getParameter('program_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $course->setProgram($newFilename);
            }


            /** @var UploadedFile $avatarFile */
            $avatarFile = $form->get('image')->getData();

            if ($avatarFile) {
                $newFilename = uniqid() . '.' . $avatarFile->guessExtension();

                try {
                    $avatarFile->move(
                        $this->getParameter('course_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $course->setImage($newFilename);
            }



            $course->setCreatedAt(new \DateTime());
            $course->setSlug($slugify->slugify($course->getName()));
            $manager->persist($course);
            $manager->flush();

            $this->addFlash('success', 'Le cours a bien été créé.');
            return $this->redirectToRoute('acp_courses');
        }
        return $this->render('admin/addCourses.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/acp/courses/{id}/del", name="delCourse")
     * @param Course $course
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_ADMIN")
     */
    public function delCourse(Course $course, EntityManagerInterface $manager): Response
    {

        $manager->remove($course);
        $manager->flush();
        $this->addFlash('success', 'Vous venez de supprimer le cours.');

        return $this->redirectToRoute('acp_courses');
    }

    /**
     * @Route("/acp/courses/{id}/publish", name="editPublish")
     * @param Course $course
     * @param $role
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_ADMIN")
     */
    public function editPublish(Course $course, EntityManagerInterface $manager): Response
    {
        if($course->getIsPublished() == 1) {
            $course->setIsPublished(0);
        } else {
            $course->setIsPublished(1);
        }

        $this->addFlash('success', 'Vous venez de changer le statut de publication du cours.');
        $manager->flush();

        return $this->redirectToRoute('acp_courses');
    }


}
