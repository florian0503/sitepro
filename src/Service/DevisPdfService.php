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
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
