<?php

namespace App\Controller;

use App\Entity\CourseRegister;
use App\Entity\UserMessage;
use App\Form\CommentType;
use App\Form\PasswordType;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function index(): Response
    {

        $user = $this->getUser();
        $coursesRegisters =  $user->getCourseRegisters();


        return $this->render('profil/index.html.twig', [
            'coursesRegisters' => $coursesRegisters,
        ]);
    }

    /**
     * @Route("/profil/edit/", name="editprofil")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_USER")
     */
    public function editProfil(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $avatarFile */
            $avatarFile = $form->get('image')->getData();

            if ($avatarFile) {
                $newFilename = uniqid() . '.' . $avatarFile->guessExtension();

                try {
                    $avatarFile->move(
                        $this->getParameter('profil_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $user->setImage($newFilename);
            }

            $manager->flush();
            $this->addFlash('success', 'Votre profil a été changé avec succès.');
            return $this->redirectToRoute('profil');
        }
        return $this->render('profil/edit.html.twig',['form' => $form->createView()]);
    }


    /**
     * @Route("/profil/password", name="editpassword")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @IsGranted("ROLE_USER")
     */
    public function editPassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(PasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->flush();
            $this->addFlash('success', 'Le mot de passe a été changé avec succès.');
            return $this->redirectToRoute('profil');
        }
        return $this->render('profil/editpassword.html.twig',['form' => $form->createView()]);
    }


    /**
     * @Route("/profil/comments", name="comments")
     * @IsGranted("ROLE_USER")
     */
    public function comments()
    {
        $messages = $this->getUser()->getUserMessages();

        return $this->render('profil/comments.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/profil/comments/{id}/del", name="delMessage")
     * @param UserMessage $userMessage
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_USER")
     */
    public function delMessage(UserMessage $userMessage, EntityManagerInterface $manager): Response
    {

        $this->addFlash('success', 'Vous avez supprimé votre notification');

        $manager->remove($userMessage);
        $manager->flush();

        return $this->redirectToRoute('profil');
    }

    /**
     * @Route("/profil/comment/{id}/edit", name="editMessage")
     * @param UserMessage $userMessage
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_USER")
     */
    public function editMessage(UserMessage $userMessage, Request $request, EntityManagerInterface $manager): Response
    {

        $user =$this->getUser();
        $comment = $userMessage->getComment();

        if($user != $comment->getAuthor()) {
            return $this->redirectToRoute('profil');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Vous venez de changer votre commentaire.');
            $manager->persist($comment);
            $manager->flush();

            // Suppression du User_Message
            $manager->remove($userMessage);
            $manager->flush();


            return $this->redirectToRoute('profil');
        }



        return $this->render('profil/editComment.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profil/delcourses/{id}", name="delCourses")
     * @param CourseRegister $courseRegister
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_USER")
     */
    public function delCourses(CourseRegister $courseRegister, EntityManagerInterface $manager): Response
    {


        $this->addFlash('success', 'Vous êtes bien désinscris du cours.');
        $manager->remove($courseRegister);
        $manager->flush();

        return $this->redirectToRoute('profil');
    }

    /**
     * @Route("/profil/cart/", name="cart")
     * @IsGranted("ROLE_USER")
     */
    public function cart(): Response
    {

        $carts = $this->getUser()->getCourseRegisters();

        return $this->render('profil/cart.html.twig', [
            'carts' => $carts,
        ]);
    }

    /**
     * @Route("/profil/cart/{id}/validation", name="cartValidation")
     * @IsGranted("ROLE_USER")
     * @param EntityManagerInterface $manager
     */
    public function cartValidation(CourseRegister $courseRegister, EntityManagerInterface $manager): Response
    {

        if ($courseRegister->getUser() == $this->getUser()) {
            $this->addFlash('success', 'Vous avez bien inscrit au cours.');
            $courseRegister->setRegistered(1);
            $manager->flush();

            return $this->redirectToRoute('cart');
        }

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/profil/cart/{id}/delete", name="delCart")
     * @param CourseRegister $courseRegister
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_USER")
     */
    public function delCart(CourseRegister $courseRegister, EntityManagerInterface $manager): Response
    {


        $this->addFlash('success', 'Vous avez retiré le cours du panier.');
        $manager->remove($courseRegister);
        $manager->flush();

        return $this->redirectToRoute('cart');
    }

   

}
