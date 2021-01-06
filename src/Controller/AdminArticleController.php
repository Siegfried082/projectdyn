<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\ArticlesType;
use App\Form\EditArticlesType;
use App\Repository\NewsRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticleController extends AbstractController
{
    /**
     * @Route("/acp/news", name="acp_News")
     * @param NewsRepository $repositoryNews
     * @IsGranted("ROLE_ADMIN")
     */
    public function News(NewsRepository $repositoryNews): Response
    {
        $news = $repositoryNews->findAll();
        return $this->render('admin/articles.html.twig', [
            'news' => $news
        ]);
    }

    /**
     * @Route("/acp/new/{id}/", name="showNews")
     * @param int $id
     * @param NewsRepository $repositoryNews
     * @IsGranted("ROLE_ADMIN")
     */
    public function showNews(NewsRepository $repositoryNews, int $id): Response
    {


        $new = $repositoryNews->find($id);
        return $this->render('admin/showArticles.html.twig', [
            'new' => $new
        ]);
    }


    /**
     * @Route("/acp/news/add", name="acp_AddNews")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param EntityManagerInterface $manager

     */
    public function addNews(Request $request, EntityManagerInterface $manager): Response
    {
        $news = new News();
        $slugify = new Slugify();

        $form = $this->createForm(ArticlesType::class, $news);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $news->setCreatedAt(new \DateTime());
            $news->setSlug($slugify->slugify($news->getName()));

            /** @var UploadedFile $newsFile */
            $newsFile = $form->get('image')->getData();

            if ($newsFile) {
                $newFilename = uniqid() . '.' . $newsFile->guessExtension();

                try {
                    $newsFile->move(
                        $this->getParameter('article_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $news->setImage($newFilename);
            }
            $manager->persist($news);
            $manager->flush();

            $this->addFlash('success', 'Vous venez d\'ajouter un article.');
            return $this->redirectToRoute('acp_News');
        }
        return $this->render('admin/addArticles.html.twig',['form' => $form->createView()]);
    }

    /**
     * @Route("/acp/new/{id}/edit", name="acp_EditNews")
     * @param News $news
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_ADMIN")
     */
    public function editNews(News $news, Request $request, EntityManagerInterface $manager): Response
    {

        $slugify = new Slugify();

        $form = $this->createForm(EditArticlesType::class, $news);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $news->setCreatedAt(new \DateTime());
            $news->setSlug($slugify->slugify($news->getName()));

            /** @var UploadedFile $newsFile */
            $newsFile = $form->get('image')->getData();

            if ($newsFile) {
                $newFilename = uniqid() . '.' . $newsFile->guessExtension();

                try {
                    $newsFile->move(
                        $this->getParameter('article_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }

                $news->setImage($newFilename);
            }

            $manager->persist($news);
            $manager->flush();

            $this->addFlash('success', 'Vous venez d\'ajouter un article.');
            return $this->redirectToRoute('acp_News');
        }
        return $this->render('admin/editArticles.html.twig',['form' => $form->createView(), 'news' => $news]);
    }

    /**
     * @Route("/acp/news/{id}/del", name="delNews")
     * @param News $news
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_ADMIN")
     */
    public function delNews(News $news, EntityManagerInterface $manager): Response
    {

        $manager->remove($news);
        $manager->flush();
        $this->addFlash('success', 'Vous venez de supprimer une actualité.');

        return $this->redirectToRoute('acp_News');
    }
}
