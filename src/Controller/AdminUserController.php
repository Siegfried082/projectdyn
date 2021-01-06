<?php

namespace App\Controller;

use App\Entity\CourseRegister;
use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserController extends AbstractController
{




    /**
     * @Route("/acp/users", name="acp_users")
     * @IsGranted("ROLE_ADMIN")
     * @param UserRepository $repositoryUsers
     */
    public function users(UserRepository $repositoryUsers): Response
    {
        $users = $repositoryUsers->findAll();
        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/acp/user/add", name="acp_addUser")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addUser(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $role[] = 'ROLE_USER';

        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setRoles($role);
            $user->setCreatedAt(new \DateTime());
            $user->setUpdatedAt(new \DateTime());
            $user->setLastLogAt(new \DateTime());
            $user->setIsDisabled('0');

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
            } else {
                $user->setImage('default.png');
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'L\'utilisateur à bien été créé.');
            return $this->redirectToRoute('acp_users');
        }
        return $this->render('admin/addUsers.html.twig',['form' => $form->createView()]);
    }

    /**
     * @Route("/acp/users/{id}/del", name="delUser")
     * @param User $user
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_ADMIN")
     */
    public function delUser(User $user, EntityManagerInterface $manager): Response
    {


        $this->addFlash('success', 'L\'utilisateur à bien été supprimé.');
        //$manager->remove($user);
        //$manager->flush();

        return $this->redirectToRoute('acp_users');
    }


    /**
     * @Route("/acp/users/{id}/", name="showUser")
     * @param int $id
     * @param UserRepository $repositoryUsers
     * @IsGranted("ROLE_ADMIN")
     */
    public function showUser(UserRepository $repositoryUsers, int $id): Response
    {

        $user = $repositoryUsers->find($id);
        $coursesRegisters =  $user->getCourseRegisters();
        return $this->render('admin/showUSers.html.twig', [
            'user' => $user,
            'coursesRegisters' => $coursesRegisters
        ]);
    }

    /**
     * @Route("/acp/admins", name="acp_admins")
     * @IsGranted("ROLE_ADMIN")
     * @param UserRepository $repositoryUsers
     */
    public function admins(UserRepository $repositoryUsers): Response
    {
        $users = $repositoryUsers->findUsersByRole('ROLE_ADMIN');

        return $this->render('admin/admins.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/acp/admins/{id}/del", name="delAdmin")
     * @param User $user
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_ADMIN")
     */
    public function delAdmin(User $user, EntityManagerInterface $manager): Response
    {

        $this->addFlash('success', 'Vous avez rétiré les permission de l\'utilisateur.');

        $user->setRoles(["ROLE_USER"]);
        $manager->flush();

        return $this->redirectToRoute('acp_admins');
    }

    /**
     * @Route("/acp/admins/{id}/{role}/promote", name="promoteAdmin")
     * @param User $user
     * @param $role
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_ADMIN")
     */
    public function promoteAdmin(User $user, EntityManagerInterface $manager, $role): Response
    {

        $this->addFlash('success', 'Vous venez de promote un utilisateur.');

        $user->setRoles([$role]);
        $manager->flush();

        return $this->redirectToRoute('acp_admins');
    }

    /**
     * @Route("/acp/user/{id}/delCourseRegister", name="delCourseRegister")
     * @param CourseRegister $CourseRegister
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_ADMIN")
     */
    public function delCourseRegister(CourseRegister $CourseRegister, EntityManagerInterface $manager): Response
    {

       // dd($CourseRegister);
        $manager->remove($CourseRegister);
        $manager->flush();
        $this->addFlash('success', 'Vous venez de supprimer cet élève du cours.');

        return $this->redirectToRoute('acp_users');
    }



}
