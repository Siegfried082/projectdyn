<?php

namespace App\Service;

use App\Entity\Contact;
use Twig\Environment;

class ContactService
{
    private $mailer;
    private $renderer;

    // La classe Environment permet d'utiliser Twig
    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;

    }

    public function sendMail(Contact $contact, $admin)
    {
        $message = (new \Swift_Message())
            ->setFrom($contact->getEmail())
            ->setTo($admin)
            ->setReplyTo($contact->getEmail())
            ->setSubject($contact->getSubject())
            ->setBody($this->renderer->render('email/contact.html.twig',['contact' => $contact]),'text/html');

        $this->mailer->send($message);

    }

}