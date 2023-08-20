<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Container6dl9C24\PaginatorInterface_82dac15;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->partitionAll(),
        ]);
    }
///////////////////////////////////////////////////////   FRONT   ////////////////////////////////////////////////////////////////
    #[Route('/indexFront', name: 'app_commande_indexFront')]
    public function indexFront(CommandeRepository $commandeRepository , PanierController $panierController, PaginatorInterface $paginator, Request $request): Response
    {
        $commandesUserAll = $commandeRepository->getCommandesByUserPartitioned($panierController,$request);
        $paginateME = $commandeRepository->getNonCanceledCommande($commandesUserAll);
        $lastCmd = count($paginateME);
        if ($lastCmd == 0){
            $lastCmd = 1;
        }
        $commandes = $paginator->paginate(
            $paginateME, /* query NOT result */
            $request->query->getInt('page', $lastCmd), /*page number*/
            1 /*limit per page*/
        );

        $cmd = new Commande();
        $cmd->setIdcmd(1);

        return $this->render('commande/indexFront.html.twig', [
            'commandes' => $commandes,
        ]);
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CommandeRepository $commandeRepository): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeRepository->save($commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{idcmd}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{idcmd}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, CommandeRepository $commandeRepository): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeRepository->save($commande, true);

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{idcmd}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, CommandeRepository $commandeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getIdcmd(), $request->request->get('_token'))) {
            $commandeRepository->remove($commande, true);
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
    /*
     * fonctions speciales
     */

    #[Route('/{idcmd}/annuler', name: 'app_commande_annuler')]
    public function annulerCommande(Commande $commande , CommandeRepository $commandeRepository){

        $commandeRepository->updateEtat($commande , 'annulée');


        return $this->redirectToRoute('app_commande_indexFront');
    }

}
