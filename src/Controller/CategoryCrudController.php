<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Category1Type;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category/crud')]
final class CategoryCrudController extends AbstractController
{
    #[Route(name: 'app_category_crud_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        // lecture : tout utilisateur connecté si tu veux
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('category_crud/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_category_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // création : ADMIN / DIRECTEUR uniquement
        if (
            !$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_DIRECTEUR')
        ) {
            throw $this->createAccessDeniedException();
        }

        $category = new Category();
        $form = $this->createForm(Category1Type::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_category_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category_crud/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_crud_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        // lecture seule, tu peux laisser ouvert
        return $this->render('category_crud/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        // édition : à partir de MANAGER / SENIOR
        if (
            !$this->isGranted('ROLE_MANAGER')
            && !$this->isGranted('ROLE_SENIOR')
            && !$this->isGranted('ROLE_DIRECTEUR')
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(Category1Type::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_category_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category_crud/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        // suppression : ADMIN / DIRECTEUR uniquement
        if (
            !$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_DIRECTEUR')
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_category_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
