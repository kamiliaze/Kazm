<?php

namespace App\Command;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fix-images',
    description: 'Corrige les noms d\'images dans la base de données',
)]
class FixImagesCommand extends Command
{
    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Mapping EXACT des références aux noms de fichiers RÉELS
        $correctMapping = [
            // BRUMES
            'BR-OUDJAS05' => 'oud-jasmin.jpg',
            'BR-FR01' => 'fruit-rouge.jpg',
            'BR-FO02' => 'fleur-orange.jpg',
            'BR-RM03' => 'rose-matik.jpg',
            'BR-OUD04' => 'oud.jpg',
            'BR-MUSCBL06' => 'misk-blanc.jpg',
            'BR-AMBER07' => 'amber.jpg',
            'BR-MONOI08' => 'monoi.jpg',

            // CRÈMES
            'CC-FR01' => 'creme-fruit-rouge.jpg',
            'CC-FO02' => 'creme-fleure-d-oranger.jpg',
            'CC-RM03' => 'creme-rose-mystique.jpg',
            'CC-OUD04' => 'creme-oud.jpg',
            'CC-OUDJS05' => 'creme-oud-jasmin.jpg',
            'CC-MB06' => 'creme-musc-blanc.jpg',
            'CC-A07' => 'creme-amber.jpg',
            'CC-MO08' => 'creme-monoi.jpg',

            // GELS DOUCHE
            'GD-001' => 'gel-douche-amber.jpg',
            'GD-002' => 'gel-douche-fleur-d-oranger.jpg',
            'GD-003' => 'gel-douche-fruit-rouge.jpg',
            'GD-004' => 'gel-douche-monoi.jpg',
            'GD-005' => 'gel-douche-musc-blanc.jpg',
            'GD-006' => 'gel-douche-oud-jasmin.jpg',
            'GD-007' => 'gel-douche-oud.jpg',
            'GD-008' => 'gel-douche-rose-mystique.jpg',

            // BAUMES DÉODORANTS
            'BD001' => 'baume-deodorant-amber.jpg',
            'BD002' => 'baume-deodorant-fleur-d-oranger.jpg',
            'BD003' => 'baume-deodorant-fruit-rouge.jpg',
            'BD004' => 'baume-deodorant-monoi.jpg',
            'BD005' => 'baume-deodorant-musc-blanc.jpg',
            'BD006' => 'baume-deodorant-oud-jasmin.jpg',
            'BD007' => 'baume-deodorant-oud.jpg',
            'BD008' => 'baume-deodorant-rose-mystique.jpg',

            // AUTOBRONZANTS
            'AUTO-L' => 'autobronzat-light.jpg',
            'AUTO-D' => 'autobronzat-dark.jpg',

            // PARFUMS
            'PRF-001' => 'parfum-amber.jpg',
            'PRF-002' => 'parfum-fleur-d-oranger.jpg',
            'PRF-003' => 'parfum-fruit-rouge.jpg',
            'PRF-004' => 'parfum-monoi.jpg',
            'PRF-005' => 'parfum-musc-blanc.jpg',
            'PRF-006' => 'parfum-oud-jasmin.jpg',
            'PRF-007' => 'parfum-oud.jpg',
            'PRF-008' => 'parfum-rose-mystique.jpg',
        ];

        $io->title('🔧 Correction des noms d\'images dans la base');
        $updated = 0;

        foreach ($correctMapping as $ref => $imageName) {
            $product = $this->productRepository->findOneBy(['reference' => $ref]);
            if ($product) {
                $old = $product->getImage();
                $product->setImage($imageName);
                $this->entityManager->persist($product);
                if ($old !== $imageName) {
                    $io->writeln("✅ {$product->getName()} ({$ref}): <comment>\"$old\"</comment> → <info>\"$imageName\"</info>");
                    $updated++;
                }
            }
        }

        $this->entityManager->flush();

        $io->success("🎉 $updated produits mis à jour !");
        return Command::SUCCESS;
    }
}
