<?php

namespace App\Repository;

use App\Entity\Movement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MovementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movement::class);
    }

    /**
     * Récupérer l'historique des mouvements d'un produit
     */
    public function findByProduct(int $productId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.product = :productId')
            ->setParameter('productId', $productId)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Statistiques des mouvements par type
     */
    public function getMovementStats(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.type, COUNT(m.id) as count, SUM(m.quantity) as total')
            ->groupBy('m.type')
            ->getQuery()
            ->getResult();
    }
}
