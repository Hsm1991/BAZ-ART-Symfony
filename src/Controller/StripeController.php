<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Facture;
use App\Entity\Historiquevente;
use App\Repository\CommandeRepository;
use App\Repository\CoursRepository;
use App\Repository\FactureRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/stripe')]
class StripeController extends AbstractController
{
    #[Route('/', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/{idcmd}/checkout', name: 'app_stripe_checkout')]
    public function checkout(Commande $order , CommandeRepository $repo , PanierController $pc , Request $request): Response
    {
        $total = $repo->getTotalCommande($order->getIdcmd(), $pc , $request);

        $stripe = new \Stripe\StripeClient(

            'sk_test_51MAfj4ES68ocXgHIpdFomTcCjhbHmiAxj1muKgPyOj3taEtnkmP3nPXZwmwMWQUC9nq44xiAYN6RfyPY4mKmFOvn00fV8NftXF'
        );


        $session =$stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'customer_email' => 'customer@example.com',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Total commande',
                        ],
                        'unit_amount' => $total*100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',

            'success_url' => $this->generateUrl('app_stripe_success', ['idcmd'=>$order->getIdcmd()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_stripe_echec', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        dump($session);

        return $this->redirect($session->url, 303);
    }


    #[Route('{idcmd}/checkout/success', name: 'app_stripe_success')]
    public function successUrl(Commande $commande , CommandeRepository $repo , PanierController $pc , ManagerRegistry $doctrine,Request $request): Response
    {
        $facture = new Facture();
        $facture->setDatecmd($commande->getDatecmd());
        $facture->setIdcmd($commande->getIdcmd());
        $facture->setIduser($commande->getIdpanier());
        $facture->setMontant($repo->getTotalCommande($commande->getIdcmd() , $pc,$request) );
        $doctrine->getManager()->persist($facture);
        $doctrine->getManager()->flush();
        //////////////////commande ---> payée//////////////
        $repo->updateEtat($commande , 'payée');
        /////////////////update historiquevente/////////////
        $commandeById = $repo->findBy(['idcmd'=>$commande->getIdcmd()]);
        foreach($commandeById as $prod) {
            $hist = new Historiquevente();
            $hist->setIdprod($prod->getIdprod());
            $hist->setDatevent(date('d/m/Y'));
            $hist->setPrixvente($prod->getPrixremise());
            $hist->setQtevendue($prod->getQuantite());
            $doctrine->getManager()->persist($hist);
            $doctrine->getManager()->flush();
        }

        return $this->render('stripe/success.html.twig');
    }



    #[Route('/checkout/echec', name: 'app_stripe_echec')]
    public function cancelUrl(): Response
    {
        return $this->render('stripe/cancel.html.twig');
    }


}
