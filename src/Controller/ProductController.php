<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/products')]  // ← Nouveau (avec "s")

class ProductController extends AbstractController
{
    #[Route('', name: 'product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $params): Response
    {
        // Vérifier si l'utilisateur a le rôle ADMIN
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', '🔒 Vous n\'avez pas la permission de créer un produit. Contactez un administrateur.');
            return $this->redirectToRoute('product_index');
        }

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $uploadsDir = $params->get('kernel.project_dir') . '/public/images/products';
                // Créer le dossier s'il n'existe pas
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }
                
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                
                try {
                    $imageFile->move($uploadsDir, $newFilename);
                    $product->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', '❌ Erreur lors de l\'upload de l\'image.');
                }
            }
            
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', '✅ Produit créé avec succès !');
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, ParameterBagInterface $params): Response
    {
        // Vérifier si l'utilisateur a le rôle ADMIN
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', '🔒 Vous n\'avez pas la permission de modifier ce produit. Contactez un administrateur.');
            return $this->redirectToRoute('product_index');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $uploadsDir = $params->get('kernel.project_dir') . '/public/images/products';
                // Créer le dossier s'il n'existe pas
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0755, true);
                }
                
                // Supprimer l'ancienne image si elle existe
                if ($product->getImage()) {
                    $oldImagePath = $uploadsDir . '/' . $product->getImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                
                try {
                    $imageFile->move($uploadsDir, $newFilename);
                    $product->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', '❌ Erreur lors de l\'upload de l\'image.');
                }
            }
            
            $entityManager->flush();
            $this->addFlash('success', '✅ Produit modifié avec succès !');
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur a le rôle ADMIN
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', '🔒 Vous n\'avez pas la permission de supprimer ce produit. Contactez un administrateur.');
            return $this->redirectToRoute('product_index');
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
            $this->addFlash('success', '✅ Produit supprimé avec succès !');
        }

        return $this->redirectToRoute('product_index');
    }
}
