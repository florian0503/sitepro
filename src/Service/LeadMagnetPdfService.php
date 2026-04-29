<?php

declare(strict_types=1);

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class LeadMagnetPdfService
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function generate(): string
    {
        $html = $this->twig->render('pdf/lead_magnet.html.twig');

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
