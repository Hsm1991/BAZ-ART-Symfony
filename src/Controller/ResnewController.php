<?php

namespace App\Controller;

use App\Entity\Res;
use App\Form\ResType;
use App\Repository\ResRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/resnew')]
class ResnewController extends AbstractController
{
    #[Route('/', name: 'app_resnew_index', methods: ['GET'])]
    public function index(ResRepository $resRepository): Response
    {
        return $this->render('resnew/index.html.twig', [
            'res' => $resRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_resnew_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ResRepository $resRepository): Response
    {
        $re = new Res();
        $form = $this->createForm(ResType::class, $re);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resRepository->save($re, true);
            $resRepository->sendsms();

            return $this->redirectToRoute('app_resnew_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('resnew/new.html.twig', [
            're' => $re,
            'form' => $form,
        ]);
    }

    #[Route('/{idRes}', name: 'app_resnew_show', methods: ['GET'])]
    public function show(Res $re): Response
    {
        return $this->render('resnew/show.html.twig', [
            're' => $re,
        ]);
    }

    #[Route('/{idRes}/edit', name: 'app_resnew_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Res $re, ResRepository $resRepository): Response
    {
        $form = $this->createForm(ResType::class, $re);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resRepository->save($re, true);

            return $this->redirectToRoute('app_resnew_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('resnew/edit.html.twig', [
            're' => $re,
            'form' => $form,
        ]);
    }

    #[Route('/{idRes}', name: 'app_resnew_delete', methods: ['POST'])]
    public function delete(Request $request, Res $re, ResRepository $resRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$re->getIdRes(), $request->request->get('_token'))) {
            $resRepository->remove($re, true);
        }

        return $this->redirectToRoute('app_resnew_index', [], Response::HTTP_SEE_OTHER);
    }
}
