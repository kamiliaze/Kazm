<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response
    {
        // Produits en alerte (quantité < seuil d'alerte)
       $alertProducts = $productRepository->createQueryBuilder('p')
    ->where('p.quantity <= p.alertThreshold')
    ->orderBy('p.quantity', 'ASC')
    ->getQuery()
    ->getResult();


        // Statistiques par catégorie
        $statsByCategory = $categoryRepository->createQueryBuilder('c')
            ->select('c.name, COUNT(p.id) as productCount, SUM(p.quantity) as totalStock')
            ->leftJoin('c.products', 'p')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();

        // Stock total par fournisseur
        $statsBySupplier = $productRepository->createQueryBuilder('p')
            ->select('p.supplier, COUNT(p.id) as productCount, SUM(p.quantity) as totalStock')
            ->groupBy('p.supplier')
            ->getQuery()
            ->getResult();

        // Derniers produits ajoutés (5 derniers)
        $recentProducts = $productRepository->createQueryBuilder('p')
            ->orderBy('p.addedAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        // Statistiques globales
        $totalProducts = $productRepository->count([]);
        $totalStock = $productRepository->createQueryBuilder('p')
            ->select('SUM(p.quantity)')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->render('dashboard/index.html.twig', [
            'alertProducts' => $alertProducts,
            'statsByCategory' => $statsByCategory,
            'statsBySupplier' => $statsBySupplier,
            'recentProducts' => $recentProducts,
            'totalProducts' => $totalProducts,
            'totalStock' => $totalStock,
        ]);
    }
}
