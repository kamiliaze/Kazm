<?php

namespace App\Controller;

use App\Entity\Movement;
use App\Form\MovementType;
use App\Repository\MovementRepository;
use App\Service\PdfGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/movement')]
class MovementController extends AbstractController
{
    #[Route('/', name: 'app_movement_index', methods: ['GET'])]
    public function index(MovementRepository $movementRepository): Response
    {
        // Senior, Manager, Directeur, Admin seulement
        if (
            !$this->isGranted('ROLE_SENIOR')
            && !$this->isGranted('ROLE_MANAGER')
            && !$this->isGranted('ROLE_DIRECTEUR')
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('movement/index.html.twig', [
            'movements' => $movementRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_movement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (
            !$this->isGranted('ROLE_SENIOR')
            && !$this->isGranted('ROLE_MANAGER')
            && !$this->isGranted('ROLE_DIRECTEUR')
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException();
        }

        $movement = new Movement();
        $form = $this->createForm(MovementType::class, $movement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movement->setUser($this->getUser());
            $product = $movement->getProduct();

            if ($movement->getType() === 'ENTREE') {
                $product->setQuantity($product->getQuantity() + $movement->getQuantity());
            } else {
                $newQuantity = $product->getQuantity() - $movement->getQuantity();
                if ($newQuantity < 0) {
                    $this->addFlash(
                        'error',
                        'Stock insuffisant ! Quantité disponible : '.$product->getQuantity()
                    );

                    return $this->render('movement/new.html.twig', [
                        'form' => $form,
                    ]);
                }
                $product->setQuantity($newQuantity);
            }

            $entityManager->persist($movement);
            $entityManager->flush();

            $this->addFlash('success', 'Mouvement enregistré avec succès !');

            return $this->redirectToRoute('app_movement_index');
        }

        return $this->render('movement/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/{id}', name: 'app_movement_by_product', methods: ['GET'])]
    public function byProduct(int $id, MovementRepository $movementRepository): Response
    {
        if (
            !$this->isGranted('ROLE_SENIOR')
            && !$this->isGranted('ROLE_MANAGER')
            && !$this->isGranted('ROLE_DIRECTEUR')
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('movement/by_product.html.twig', [
            'movements' => $movementRepository->findByProduct($id),
        ]);
    }

    #[Route('/export/sorties', name: 'app_movement_export_sorties', methods: ['GET'])]
    public function exportSorties(MovementRepository $movementRepository, PdfGenerator $pdfGenerator): Response
    {
        if (
            !$this->isGranted('ROLE_SENIOR')
            && !$this->isGranted('ROLE_MANAGER')
            && !$this->isGranted('ROLE_DIRECTEUR')
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException();
        }

        $movements = $movementRepository->findBy(
            ['type' => 'SORTIE'],
            ['createdAt' => 'DESC']
        );

        $pdfContent = $pdfGenerator->generateMovementReport($movements, 'SORTIE');

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rapport_sorties_'.date('Y-m-d').'.pdf"',
        ]);
    }

    #[Route('/export/entrees', name: 'app_movement_export_entrees', methods: ['GET'])]
    public function exportEntrees(MovementRepository $movementRepository, PdfGenerator $pdfGenerator): Response
    {
        if (
            !$this->isGranted('ROLE_SENIOR')
            && !$this->isGranted('ROLE_MANAGER')
            && !$this->isGranted('ROLE_DIRECTEUR')
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException();
        }

        $movements = $movementRepository->findBy(
            ['type' => 'ENTREE'],
            ['createdAt' => 'DESC']
        );

        $pdfContent = $pdfGenerator->generateMovementReport($movements, 'ENTREE');

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rapport_entrees_'.date('Y-m-d').'.pdf"',
        ]);
    }

    #[Route('/export/all', name: 'app_movement_export_all', methods: ['GET'])]
    public function exportAll(MovementRepository $movementRepository, PdfGenerator $pdfGenerator): Response
    {
        if (
            !$this->isGranted('ROLE_SENIOR')
            && !$this->isGranted('ROLE_MANAGER')
            && !$this->isGranted('ROLE_DIRECTEUR')
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException();
        }

        $movements = $movementRepository->findBy([], ['createdAt' => 'DESC']);
        $pdfContent = $pdfGenerator->generateMovementReport($movements, 'ALL');

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rapport_complet_'.date('Y-m-d').'.pdf"',
        ]);
    }

    #[Route('/export/{id}', name: 'app_movement_export_single', methods: ['GET'])]
    public function exportSingle(Movement $movement, PdfGenerator $pdfGenerator): Response
    {
        if (
            !$this->isGranted('ROLE_SENIOR')
            && !$this->isGranted('ROLE_MANAGER')
            && !$this->isGranted('ROLE_DIRECTEUR')
            && !$this->isGranted('ROLE_ADMIN')
        ) {
            throw $this->createAccessDeniedException();
        }

        $pdfContent = $pdfGenerator->generateSingleMovementReport($movement);
        $filename   = 'mouvement_'.$movement->getType().'_'.$movement->getId().'_'.date('Y-m-d').'.pdf';

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
