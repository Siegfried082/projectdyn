<?php

namespace App\Controller;

use App\Entity\Bans;
use App\Form\BansType;
use App\Repository\BansRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminBansController extends AbstractController
{
    /**
     * @Route("/acp/bans", name="acp_bans")
     * @param BansRepository $repositoryBans
     * @IsGranted("ROLE_ADMIN")
     */
    public function bans(BansRepository $repositoryBans): Response
    {
        $bans = $repositoryBans->findAll();

        return $this->render('admin/bans.html.twig', [
            'bans' => $bans
        ]);
    }

    /**
     * @Route("/acp/bans/add", name="acp_addbans")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param EntityManagerInterface $manager

     */
    public function addBans(Request $request, EntityManagerInterface $manager): Response
    {
        $ban = new Bans();



        $form = $this->createForm(BansType::class, $ban);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $now = new \DateTime('now', new \DateTimeZone('Europe/Brussels'));
            $ban->setDate($now);
            $manager->persist($ban);
            $manager->flush();

            $this->addFlash('success', 'Vous venez d\'ajouter une régle de bannissement.');
            return $this->redirectToRoute('acp_bans');
        }
        return $this->render('admin/addBans.html.twig',['form' => $form->createView()]);
    }

    /**
     * @Route("/acp/bans/{id}/del", name="delbans")
     * @param Bans $bans
     * @param EntityManagerInterface $manager
     * @IsGranted("ROLE_ADMIN")
     */

    public function delBans(Bans $bans, EntityManagerInterface $manager): Response
    {

        $manager->remove($bans);
        $manager->flush();

        $this->addFlash('success', 'Vous avez rétiré les règle de bannissement de l\'utilisateur.');
        return $this->redirectToRoute('acp_bans');
    }

}

