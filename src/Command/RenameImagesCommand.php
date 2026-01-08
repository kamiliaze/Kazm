<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:rename-images',
    description: 'Renomme les images pour correspondre aux produits',
)]
class RenameImagesCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $imageDir = __DIR__ . '/../../public/images/products';

        // Mapping basé sur les noms de fichiers détectés
        $renameMap = [
            // GEL DOUCHE
            'gel-douche-oud' => 'gel-oud.jpg',
            'gel-douche-oud-jasmine' => 'gel-oud-jasmin.jpg',
            'gel-douche-amber' => 'gel-amber.jpg',
            'gel-douche-monoi' => 'gel-monoi.jpg',
            'shower-cream-oud' => 'gel-oud.jpg',
            'shower-cream-oud-jasmine' => 'gel-oud-jasmin.jpg',
            'shower-cream-amber' => 'gel-amber.jpg',
            'shower-cream-monoi' => 'gel-monoi.jpg',
            
            // CRÈME CORPORELLE
            'creme-oud' => 'creme-oud.jpg',
            'creme-oud-jasmine' => 'creme-oud-jasmin.jpg',
            'creme-amber' => 'creme-amber.jpg',
            'creme-monoi' => 'creme-monoi.jpg',
            'body-cream-oud' => 'creme-oud.jpg',
            'body-cream-oud-jasmine' => 'creme-oud-jasmin.jpg',
            'body-cream-amber' => 'creme-amber.jpg',
            'body-cream-monoi' => 'creme-monoi.jpg',
            
            // DÉODORANT
            'deodorant-oud' => 'deodorant-oud.jpg',
            'deodorant-oud-jasmine' => 'deodorant-oud-jasmin.jpg',
            'deodorant-amber' => 'deodorant-amber.jpg',
            'deodorant-monoi' => 'deodorant-monoi.jpg',
            
            // PARFUM
            'parfum-oud' => 'parfum-oud.jpg',
            'parfum-oud-jasmine' => 'parfum-oud-jasmin.jpg',
            'parfum-amber' => 'parfum-amber.jpg',
            'parfum-monoi' => 'parfum-monoi.jpg',
            'fragrance-oud' => 'parfum-oud.jpg',
            'fragrance-oud-jasmine' => 'parfum-oud-jasmin.jpg',
            'fragrance-amber' => 'parfum-amber.jpg',
            'fragrance-monoi' => 'parfum-monoi.jpg',
        ];

        $files = scandir($imageDir);
        $io->title('🎨 Renommage des images');

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === '.gitkeep') continue;
            
            $filePath = $imageDir . '/' . $file;
            if (!is_file($filePath)) continue;

            $baseName = strtolower(pathinfo($file, PATHINFO_FILENAME));
            
            foreach ($renameMap as $pattern => $newName) {
                if (strpos($baseName, str_replace('-', '', $pattern)) !== false || 
                    strpos($baseName, $pattern) !== false) {
                    
                    $newPath = $imageDir . '/' . $newName;
                    
                    if ($file !== $newName) {
                        rename($filePath, $newPath);
                        $io->writeln("✅ <info>$file</info> → <comment>$newName</comment>");
                    }
                    break;
                }
            }
        }

        $io->success('🎉 Images renommées !');
        return Command::SUCCESS;
    }
}
