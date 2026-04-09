<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Devis;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class DevisPdfService
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function generate(Devis $devis): string
    {
        $html = $this->twig->render('pdf/devis.html.twig', [
            'devis' => $devis,
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('Helvetica');
        $canvas->page_text(495, 820, 'Page {PAGE_NUM} / {PAGE_COUNT}', $font, 8, [0.6, 0.6, 0.6]);

        return $dompdf->output();
    }
}
