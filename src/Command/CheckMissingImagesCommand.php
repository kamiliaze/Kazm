<?php

namespace App\Command;

use App\Repository\ProductRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:check-missing-images',
    description: 'Affiche les produits sans images',
)]
class CheckMissingImagesCommand extends Command
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $imageDir = __DIR__ . '/../../public/images/products';

        // Récupérer les fichiers réels
        $files = scandir($imageDir);
        $realFiles = [];
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && $file !== '.gitkeep' && filesize($imageDir . '/' . $file) > 1000) {
                $realFiles[strtolower($file)] = true;
            }
        }

        // Récupérer tous les produits
        $products = $this->productRepository->findAll();
        
        $io->title('🔍 Diagnostic des images');
        $io->writeln("Fichiers réels: " . count($realFiles));
        $io->writeln("Total produits: " . count($products));

        $missing = [];
        foreach ($products as $product) {
            $image = strtolower($product->getImage() ?? '');
            if (!$image || !isset($realFiles[$image])) {
                $missing[] = $product;
            }
        }

        if (count($missing) > 0) {
            $io->section("❌ Produits sans images ($" . count($missing) . "):");
            foreach ($missing as $product) {
                $io->writeln("  • {$product->getReference()} - {$product->getName()} (image: {$product->getImage()})");
            }
        } else {
            $io->success("✅ TOUS les produits ont une image !");
        }

        return Command::SUCCESS;
    }
}
