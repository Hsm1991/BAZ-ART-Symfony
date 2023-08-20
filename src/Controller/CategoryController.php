<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/type')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_user_type_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'user_types' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $userType = new Category();
        $form = $this->createForm(CategoryType::class, $userType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($userType, true);

            return $this->redirectToRoute('app_user_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $userType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_type_show', methods: ['GET'])]
    public function show(Category $userType): Response
    {
        return $this->render('category/show.html.twig', [
            'user_type' => $userType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $userType, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $userType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($userType, true);

            return $this->redirectToRoute('app_user_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'user_type' => $userType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_type_delete', methods: ['POST'])]
    public function delete(Request $request, Category $userType, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userType->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($userType, true);
        }

        return $this->redirectToRoute('app_user_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
