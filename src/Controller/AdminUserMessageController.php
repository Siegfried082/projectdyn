<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\UserMessage;
use App\Form\AdminUserMessageType;
use App\Form\UserMessageType;
use App\Repository\CommentRepository;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserMessageController extends AbstractController
{
    /**
     * @Route("/acp/message/", name="acp_message")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addMessage(Request $request, EntityManagerInterface $manager): Response
    {
        $message = new UserMessage();

        $form = $this->createForm(UserMessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($message);
            $manager->flush();

            $this->addFlash('success', 'La notification a bien été envoyée');
            return $this->redirectToRoute('acp_users');
        }
        return $this->render('admin/addMessage.html.twig',['form' => $form->createView()]);
    }

    /**
     * @Route("/acp/message/{id}/comment", name="acp_Message_Comment")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param CommentRepository $repositorycomment
     * @param int $id
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function AddMessageComment(CommentRepository $repositorycomment, Request $request, EntityManagerInterface $manager, int $id): Response
    {

        $comment = $repositorycomment->find($id);
        $message = new UserMessage();

        $form = $this->createForm(AdminUserMessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $message->setUser($comment->getAuthor());
            $message->setComment($comment);
            $manager->persist($message);
            $manager->flush();

            $comment->setIsDisabled(1);
            $comment->setReport(0);
            $manager->flush();

            $this->addFlash('success', 'La notification a bien été envoyée');
            return $this->redirectToRoute('acp_comments');
        }
        return $this->render('admin/editMessage.html.twig',[
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }
}
