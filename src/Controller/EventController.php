<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Entity\Salle;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[Route('/event')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository,PanierController $panierController): Response
    {

        return $this->render('event/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EvenementRepository $evenementRepository, SluggerInterface $slugger, EntityManagerInterface $entityManager,FlashyNotifier $flashy): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);
        $repository = $entityManager->getRepository(Evenement::class);





                $form = $this->createForm(EvenementType::class, $evenement);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $brochureFile = $form->get('image1')->getData();

                    // this condition is needed because the 'brochure' field is not required
                    // so the PDF file must be processed only when a file is uploaded
                    if ($brochureFile) {
                        $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                        // Move the file to the directory where brochures are stored
                        try {
                            $brochureFile->move(
                                $this->getParameter('personne_directory'),
                                $newFilename
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        // updates the 'brochureFilename' property to store the PDF file name
                        // instead of its contents
                        $evenement->setImage1('C:\Users\User\Desktop\TRAVAIL_FINAL\IntegrationFinale-image-like\public\uploads\personnes\\'.$newFilename);

                    }

                    $evenementRepository->save($evenement, true);
                    $this->addFlash(
                        'Success',
                        'Evènement Ajouté Avec Succés!'
                    );
                    return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);

                }



        return $this->renderForm('event/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form

        ]);

    }

    /**
     * @route("/recherche",name="rech" ,methods={"GET","POST"})
     *
     *
     */
    public function recherche(Request $req, EntityManagerInterface $entityManager)
    {
        $data = $req->get('searche');
        $repository = $entityManager->getRepository(Evenement::class);

        $event = $repository->findBy(['NomEvent' => $data]);
        return $this->render('event/index.html.twig', [
            'evenements' => $event,


        ]);
    }







    #[Route('/{id}', name: 'app_event_show_b', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('event/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EvenementRepository $evenementRepository,FlashyNotifier $flashy): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenementRepository->save($evenement, true);
            $this->addFlash(
                'Warning',
                'Evènement Modifié Avec Succés!'
            );
            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EvenementRepository $evenementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $evenementRepository->remove($evenement, true);
            $this->addFlash(
                'Warning',
                'Evènement Supprimé Avec Succés!'
            );

        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }









}
