<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:check-images',
    description: 'Vérifie les fichiers images réels',
)]
class CheckImagesCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $imageDir = __DIR__ . '/../../public/images/products';

        $io->title('📁 Fichiers images réels dans le dossier');

        $files = scandir($imageDir);
        $imageFiles = [];

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && $file !== '.gitkeep') {
                $filePath = $imageDir . '/' . $file;
                if (is_file($filePath)) {
                    $size = filesize($filePath);
                    // Afficher le nom EXACT du fichier
                    $io->writeln("  <comment>\"$file\"</comment> (" . ($size > 0 ? round($size / 1024) . 'KB' : 'vide') . ')');
                    $imageFiles[] = $file;
                }
            }
        }

        $io->writeln("\n<info>Total: " . count($imageFiles) . " fichiers</info>\n");

        return Command::SUCCESS;
    }
}
