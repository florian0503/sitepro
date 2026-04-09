<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Devis;
use App\Service\DevisPdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DevisPdfController extends AbstractController
{
    #[Route('/admin/devis/{id}/pdf', name: 'admin_devis_pdf')]
    public function __invoke(Devis $devis, DevisPdfService $pdfService): Response
    {
        $pdfContent = $pdfService->generate($devis);
        $filename = 'Devis_'.$devis->getReference().'_'.date('Ymd').'.pdf';

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }
}
