<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-products',
    description: 'Importe tous les produits brumes dans la base de données',
)]
class ImportProductsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $products = [
            ['BR001', 'Brume Fruit Rouge', 50, 10, 'Fournisseur A', 'Parfumerie', 'Lab Cosmétiques'],
            ['BR002', 'Brume Fruit d\'Oranger', 45, 10, 'Fournisseur A', 'Parfumerie', 'Lab Cosmétiques'],
            ['BR003', 'Brume Rose Mystique', 60, 10, 'Fournisseur A', 'Parfumerie', 'Lab Cosmétiques'],
            ['OUD001', 'Oud', 30, 5, 'Fournisseur B', 'Parfumerie', 'Lab Essences'],
            ['OUD002', 'Oud Jasmin', 35, 5, 'Fournisseur B', 'Parfumerie', 'Lab Essences'],
            ['MUS001', 'Musc Blanc', 40, 8, 'Fournisseur B', 'Parfumerie', 'Lab Essences'],
            ['AMB001', 'Amber', 25, 5, 'Fournisseur C', 'Parfumerie', 'Lab Luxe'],
            ['MON001', 'Monoï', 55, 10, 'Fournisseur C', 'Parfumerie', 'Lab Tropical'],
        ];

        foreach ($products as $data) {
            $product = new Product();
            $product->setReference($data[0]);
            $product->setName($data[1]);
            $product->setQuantity($data[2]);
            $product->setAlertThreshold($data[3]);
            $product->setSupplier($data[4]);
            $product->setScent($data[5]); // "Parfumerie" dans le champ scent
            $product->setLab($data[6]); // setLab au lieu de setLaboratory
            $product->setAddedAt(new \DateTimeImmutable()); // setAddedAt au lieu de setEntryDate
            $product->setEntryDate(new \DateTimeImmutable());

            $this->entityManager->persist($product);
            $io->success('Produit ajouté : ' . $data[1]);
        }

        $this->entityManager->flush();

        $io->success('Tous les produits ont été importés avec succès !');

        return Command::SUCCESS;
    }
}
