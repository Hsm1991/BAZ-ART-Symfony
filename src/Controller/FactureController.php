<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Facture;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use App\Services\PDFService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/facture')]
class FactureController extends AbstractController
{
    #[Route('/', name: 'app_facture_index', methods: ['GET'])]
    public function index(FactureRepository $factureRepository): Response
    {
        return $this->render('facture/index.html.twig', [
            'factures' => $factureRepository->findAll(),
        ]);
    }

    #[Route('/indexFront', name: 'app_facture_indexFront', methods: ['GET'])]
    public function indexFront(FactureRepository $factureRepository ,Request $request, ManagerRegistry $doctrine , PanierController $panierController) : Response
    {
        $id = $panierController->getSessionId($request);
        $repo = $doctrine->getRepository(Facture::class);
        $factures = $repo->findBy(['idFacture'=>$id]);
        return $this->render('facture/indexFront.html.twig' , ['factures' => $factures]);
    }

    #[Route('/new', name: 'app_facture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FactureRepository $factureRepository): Response
    {
        $facture = new Facture();
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $factureRepository->save($facture, true);

            return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facture/new.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/{idfacture}', name: 'app_facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
        return $this->render('facture/show.html.twig', [
            'facture' => $facture,
        ]);
    }

    #[Route('/{idfacture}/edit', name: 'app_facture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Facture $facture, FactureRepository $factureRepository): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $factureRepository->save($facture, true);

            return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facture/edit.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/{idfacture}', name: 'app_facture_delete', methods: ['POST'])]
    public function delete(Request $request, Facture $facture, FactureRepository $factureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getIdfacture(), $request->request->get('_token'))) {
            $factureRepository->remove($facture, true);
        }

        return $this->redirectToRoute('app_facture_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{idfacture}/imprimer', name: 'app_facture_imprimer')]
    public function imprimer(Facture $facture , PDFService $pdf , ManagerRegistry $doctrine , MailerInterface $mailer)
    {
        $repo = $doctrine->getRepository(Commande::class);
        $cmdList = $repo->findBy(['idcmd' => ''.$facture->getIdcmd()]);
        $html = $this->render('facture/imprimer.html.twig',['facture'=>$facture , 'commandes'=>$cmdList]);
        $email = (new Email())
            ->from('saifeddine.chobba@esprit.tn')
            ->to('beefyhs@gmail.com')
            ->subject('Votre Facture')
            ->html('ghghhgh');
//            ->htmlTemplate('facture/imprimer.html.twig')
//            ->context(['facture' => $facture , 'commandes' => $cmdList]);

        $mailer->send($email);


        $pdf->showPdf($html);

        return $this->redirectToRoute('app_facture_index');
    }
}
