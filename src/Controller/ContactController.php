<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Form\ContactType;
use App\Repository\UserRepository;
use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function contact(Request $request,UserRepository $userRepository, ContactService $contactService): Response
    {
        $contact = new Contact();
        $admins = $userRepository->findUsersByRole('ROLE_ADMIN');

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $admin = $request->get('userAdmin');
            $contactService->sendMail($contact, $admin);

            $this->addFlash('success', 'Votre message a bien été envoyé.');
            return $this->redirectToRoute('contact');

        }
        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
            'admins' => $admins
        ]);
    }
}
