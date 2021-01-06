<?php

namespace App\Service;

use App\Entity\Contact;
use App\Entity\User;
use Twig\Environment;

class RegistrationService
{
    private $mailer;
    private $renderer;

    // La classe Environment permet d'utiliser Twig
    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;

    }

    public function sendMail(User $user)
    {

        $message = (new \Swift_Message())
            ->setFrom('no-reply@weblearning.com')
            ->setTo($user->getEmail())
            ->setReplyTo($user->getEmail())
            ->setSubject('Confirmation Inscription')

            ->setBody($this->renderer->render('email/registration.html.twig',['user' => $user]),'text/html');
        $this->mailer->send($message);


    }

}