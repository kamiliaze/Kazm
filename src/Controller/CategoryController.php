<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'category_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        // Ici tu peux laisser tout utilisateur connecté voir les catégories
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/new', name: 'category_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        // Création : seulement ADMIN (et DIRECTEUR si tu veux)
        if (
            !$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_DIRECTEUR')
        ) {
            throw $this->createAccessDeniedException();
        }

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie créée avec succès.');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/categories/{id}/edit', name: 'category_edit')]
    public function edit(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        // Edition : à partir de MANAGER (tu peux ajuster)
        if (
            !$this->isGranted('ROLE_MANAGER')
            && !$this->isGranted('ROLE_SENIOR')
            && !$this->isGranted('ROLE_DIRECTEUR')
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Catégorie modifiée avec succès.');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    #[Route('/categories/{id}/delete', name: 'category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        // Suppression : uniquement ADMIN (et DIRECTEUR si tu veux)
        if (
            !$this->isGranted('ROLE_ADMIN')
            && !$this->isGranted('ROLE_DIRECTEUR')
        ) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $em->remove($category);
            $em->flush();

            $this->addFlash('success', 'Catégorie supprimée avec succès.');
        }

        return $this->redirectToRoute('category_index');
    }
}
