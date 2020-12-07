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

    public function sendMail(Contact $contact)
    {
        $message = (new \Swift_Message())
            ->setFrom($contact->getEmail())
            ->setTo('regniernicolas37@gmail.com')
            ->setReplyTo($contact->getEmail())
            ->setSubject($contact->getSubject())
            ->setBody($this->renderer->render('contact/emailbasic.html.twig',['contact' => $contact]),'text/html');
        $this->mailer->send($message);


    }

}