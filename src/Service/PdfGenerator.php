<?php

namespace App\Service;

use App\Entity\Movement;
use TCPDF;

class PdfGenerator
{
    public function generateMovementReport(array $movements, string $type = 'SORTIE'): string
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Informations du document
        $pdf->SetCreator('Kazm Stock');
        $pdf->SetAuthor('Kazm Stock');
        $pdf->SetTitle('Rapport de ' . ($type === 'SORTIE' ? 'Sorties' : 'Mouvements'));
        
        // Supprimer header/footer par défaut
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Ajouter une page
        $pdf->AddPage();
        
        // Logo et titre
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 15, 'KAZM STOCK', 0, 1, 'C');
        
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Rapport de ' . ($type === 'SORTIE' ? 'Sorties de Stock' : 'Mouvements'), 0, 1, 'C');
        $pdf->Cell(0, 10, 'Date : ' . date('d/m/Y H:i'), 0, 1, 'C');
        $pdf->Ln(5);
        
        // En-tête du tableau
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(52, 58, 64);
        $pdf->SetTextColor(255, 255, 255);
        
        $pdf->Cell(30, 8, 'Date', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Référence', 1, 0, 'C', true);
        $pdf->Cell(45, 8, 'Produit', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Quantité', 1, 0, 'C', true);
        $pdf->Cell(40, 8, 'Destination', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'Utilisateur', 1, 1, 'C', true);
        
        // Données
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $totalQuantity = 0;
        
        foreach ($movements as $movement) {
            if ($type === 'ALL' || $movement->getType() === $type) {
                $pdf->Cell(30, 7, $movement->getCreatedAt()->format('d/m/Y'), 1, 0, 'C');
                $pdf->Cell(35, 7, $movement->getProduct()->getReference(), 1, 0, 'C');
                $pdf->Cell(45, 7, substr($movement->getProduct()->getName(), 0, 25), 1, 0, 'L');
                $pdf->Cell(20, 7, $movement->getQuantity(), 1, 0, 'C');
                $pdf->Cell(40, 7, substr($movement->getDestination() ?? '-', 0, 20), 1, 0, 'L');
                $pdf->Cell(20, 7, substr($movement->getUser()->getFirstName(), 0, 10), 1, 1, 'C');
                
                $totalQuantity += $movement->getQuantity();
            }
        }
        
        // Total
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(110, 8, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(20, 8, $totalQuantity, 1, 0, 'C');
        $pdf->Cell(60, 8, '', 1, 1, 'C');
        
        // Retourner le PDF en string
        return $pdf->Output('rapport_mouvements.pdf', 'S');
    }
    public function generateSingleMovementReport(Movement $movement): string
{
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Informations du document
    $pdf->SetCreator('Kazm Stock');
    $pdf->SetAuthor('Kazm Stock');
    $pdf->SetTitle('Bon de ' . ($movement->getType() === 'SORTIE' ? 'Sortie' : 'Entrée'));
    
    // Supprimer header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Ajouter une page
    $pdf->AddPage();
    
    // En-tête
    $pdf->SetFont('helvetica', 'B', 24);
    $pdf->Cell(0, 15, 'KAZM STOCK', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', 'B', 16);
    $typeText = $movement->getType() === 'SORTIE' ? 'BON DE SORTIE' : 'BON D\'ENTREE';
    $pdf->Cell(0, 10, $typeText, 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 8, 'N° ' . str_pad($movement->getId(), 6, '0', STR_PAD_LEFT), 0, 1, 'C');
    $pdf->Ln(5);
    
    // Informations générales
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'INFORMATIONS GENERALES', 0, 1, 'L');
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(3);
    
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(50, 7, 'Date :', 0, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 7, $movement->getCreatedAt()->format('d/m/Y à H:i'), 0, 1, 'L');
    
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(50, 7, 'Utilisateur :', 0, 0, 'L');
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(0, 7, $movement->getUser()->getFirstName() . ' ' . $movement->getUser()->getLastName(), 0, 1, 'L');
    
    if ($movement->getDestination()) {
        $pdf->SetFont('helvetica', '', 11);
        $destLabel = $movement->getType() === 'SORTIE' ? 'Destination :' : 'Provenance :';
        $pdf->Cell(50, 7, $destLabel, 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 7, $movement->getDestination(), 0, 1, 'L');
    }
    
    if ($movement->getReference()) {
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(50, 7, 'Référence :', 0, 0, 'L');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 7, $movement->getReference(), 0, 1, 'L');
    }
    
    $pdf->Ln(5);
    
    // Informations produit
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'DETAILS DU PRODUIT', 0, 1, 'L');
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
    $pdf->Ln(3);
    
    // Tableau produit
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(52, 58, 64);
    $pdf->SetTextColor(255, 255, 255);
    
    $pdf->Cell(40, 8, 'Référence', 1, 0, 'C', true);
    $pdf->Cell(90, 8, 'Nom du Produit', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Quantité', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Catégorie', 1, 1, 'C', true);
    
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    
    $pdf->Cell(40, 10, $movement->getProduct()->getReference(), 1, 0, 'C');
    $pdf->Cell(90, 10, $movement->getProduct()->getName(), 1, 0, 'L');
    
    // Quantité avec couleur
    if ($movement->getType() === 'SORTIE') {
        $pdf->SetTextColor(220, 53, 69);
        $pdf->Cell(30, 10, '- ' . $movement->getQuantity(), 1, 0, 'C');
    } else {
        $pdf->SetTextColor(25, 135, 84);
        $pdf->Cell(30, 10, '+ ' . $movement->getQuantity(), 1, 0, 'C');
    }
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(30, 10, $movement->getProduct()->getCategory()->getName(), 1, 1, 'C');
    
    // Notes
    if ($movement->getNotes()) {
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 7, 'Notes :', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, $movement->getNotes(), 1, 'L');
    }
    
    // Signatures
    $pdf->Ln(15);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(95, 7, 'Signature Responsable', 'T', 0, 'C');
    $pdf->Cell(95, 7, 'Signature Destinataire', 'T', 1, 'C');
    
    return $pdf->Output('bon_mouvement.pdf', 'S');
}

}
