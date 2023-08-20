<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/front', name: 'app_reclamation_indexfront', methods: ['GET'])]
    public function indexFront(ReclamationRepository $reclamationRepository, FlashyNotifier $flashy,PanierController $panierController,Request $request): Response
    {

        $id = $panierController->getSessionId($request);


        return $this->render('reclamation/indexFront.html.twig', [
            'reclamations' => $reclamationRepository->getReclamationUser($id),
        ]);
    }

    #[Route('/back', name: 'app_reclamation_indexback', methods: ['GET'])]
    public function indexBack(ReclamationRepository $reclamationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $paginatedRec = $paginator->paginate(
            $reclamationRepository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('reclamation/indexBack.html.twig', [
            'reclamations' => $paginatedRec,
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReclamationRepository $reclamationRepository, FlashyNotifier $flashy): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setIduser('1');
            $reclamation->setRecdate(date('d/m/Y'));
            $reclamationRepository->save($reclamation, true);

            $flashy->success('reclamation cree avec succes','#');

            return $this->redirectToRoute('app_reclamation_indexfront', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/front/{id}', name: 'app_reclamation_showfront', methods: ['GET'])]
    public function showFront(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show_front.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationRepository->save($reclamation, true);

            return $this->redirectToRoute('app_reclamation_indexfront', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, ReclamationRepository $reclamationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $reclamationRepository->remove($reclamation, true);
        }

        return $this->redirectToRoute('app_reclamation_indexfront', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/plusreclamé/metier', name: 'app_reclamation_plus')]
    public function plusReclame(ReclamationRepository $reclamationRepository){

        $paiement = $reclamationRepository->findBy(['selon'=>'probleme de paiement']);
        $livraison = $reclamationRepository->findBy(['selon'=>'probleme de livraison']);
        $technique = $reclamationRepository->findBy(['selon'=>'probleme technique']);

        $a1 = ['name'=>'paiement','num'=> count($paiement)];
        $a2 = ['name' =>'livraison','num'=>count($livraison)];
        $a3 = ['name' => 'technique','num'=>count($technique)];

        $problemes =array($a1,$a2,$a3);

        return $this->render('reclamation/metier.html.twig',
            [
                'problemes'=>$problemes
            ]);

    }
}
