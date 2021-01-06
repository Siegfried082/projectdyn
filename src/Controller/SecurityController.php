<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Service\RegistrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/registration", name="registration")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder,RegistrationService $registrationService, UserRepository $userRepository): Response
    {
        $user = new User();

         if ($this->getUser()) {
             return $this->redirectToRoute('home');
         }

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if($userRepository->findOneBy(['email' => $user->getEmail()])) {
                $this->addFlash('danger', 'Cette adresse email est déjà utilisée.');
                return $this->redirectToRoute('registration');
            }
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $user->setUpdatedAt(new \DateTime());
            $user->setCreatedAt(new \DateTime());
            $user->setLastLogAt(new \DateTime());
            $user->setRoles(['ROLE_USER']);
            $user->setIsDisabled(0);
            $user->setImage('default.jpg');


            $manager->persist($user);
            $manager->flush();

            $registrationService->sendMail($user);

            $this->addFlash('success', 'Compte créé avec succès');
            return $this->redirectToRoute('app_login');
        }


        return $this->render('security/registration.html.twig', ['form' => $form->createView()]);
    }
}
