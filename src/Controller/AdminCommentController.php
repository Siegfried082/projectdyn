<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/acp/comments", name="acp_comments")
     * @IsGranted("ROLE_ADMIN")
     * @param CommentRepository $commentRepository

     */
    public function comments(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy(
            ['isDisabled' => false]
        );

        return $this->render('admin/comments.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/acp/report/", name="acp_comments_report")
     * @IsGranted("ROLE_ADMIN")
     * @param CommentRepository $commentRepository

     */
    public function commentsReport(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy(
            ['report' => true]
        );

        return $this->render('admin/commentsReport.html.twig', [
            'comments' => $comments,
        ]);
    }
}
