<?php

namespace App\Controller;

use App\Entity\Res;
use App\Entity\Salle;
use App\Form\Salle1Type;
use App\Repository\EvenementRepository;
use App\Repository\SalleRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Evenement;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;


#[Route('/salle/front')]
class SalleFrontController extends AbstractController
{
    #[Route('/', name: 'app_salle_front_index', methods: ['GET'])]
    public function index(SalleRepository $salleRepository): Response
    {
        return $this->render('salle_front/index.html.twig', [
            'salles' => $salleRepository->findAll(),
        ]);
    }

    #[Route('/s', name: 'app_event_salle_front', methods: ['GET'])]
    public function salle(SalleRepository $salleRepository): Response
    {
        return $this->render('salle_front/front.html.twig', [
            'salles' => $salleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_salle_front_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SalleRepository $salleRepository): Response
    {
        $salle = new Salle();
        $form = $this->createForm(Salle1Type::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $salleRepository->save($salle, true);

            return $this->redirectToRoute('app_salle_front_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('salle_front/new.html.twig', [
            'salle' => $salle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_salle_front_show', methods: ['GET'])]
    public function show(Salle $salle): Response
    {
        return $this->render('salle_front/show.html.twig', [
            'salle' => $salle,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_salle_front_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Salle $salle, SalleRepository $salleRepository): Response
    {
        $form = $this->createForm(Salle1Type::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $salleRepository->save($salle, true);

            return $this->redirectToRoute('app_salle_front_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('salle_front/edit.html.twig', [
            'salle' => $salle,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_salle_front_delete', methods: ['POST'])]
    public function delete(Request $request, Salle $salle, SalleRepository $salleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$salle->getId(), $request->request->get('_token'))) {
            $salleRepository->remove($salle, true);
        }

        return $this->redirectToRoute('app_salle_front_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/stripe', name: 'app_salle_front_stripe')]
    public function checkout(Salle $salle){
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
                            'name' => 'salle',
                        ],
                        'unit_amount' => $salle->getPrix()*100,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',

            'success_url' => $this->generateUrl('app_stripe_success_salle',['id'=>$salle->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_stripe_echec', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        dump($session);

        return $this->redirect($session->url, 303);
    }

    #[Route('salle/checkout/success/{id}', name: 'app_stripe_success_salle')]
    public function successUrl(\Swift_Mailer $mailer , Request $request , UserRepository $userRepository,Salle $salle,ManagerRegistry $doctrine): Response
    {


        // On crée le message
        $message = (new \Swift_Message('Nouveau contact'))
            // On attribue l'expéditeur
            ->setFrom('hanoslighrt@gmail.com')
            // On attribue le destinataire
            ->setTo('nada.bahri@esprit.tn')
            // On crée le texte avec la vue
            ->setBody(
                'Merci de confirmer ma reservation '
            )
        ;
        $mailer->send($message);
        return $this->render('stripe/success.html.twig');
    }
}
