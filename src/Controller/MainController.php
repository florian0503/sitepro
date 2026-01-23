<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\RealisationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(RealisationRepository $realisationRepository): Response
    {
        $realisations = $realisationRepository->findPublished();

        return $this->render('pages/home.html.twig', [
            'realisations' => array_slice($realisations, 0, 4),
        ]);
    }

    #[Route('/offres', name: 'app_offers')]
    public function offers(): Response
    {
        return $this->render('pages/offers.html.twig');
    }

    #[Route('/realisations', name: 'app_portfolio')]
    public function portfolio(RealisationRepository $realisationRepository, CategoryRepository $categoryRepository): Response
    {
        $realisations = $realisationRepository->findPublished();
        $categories = $categoryRepository->findAllOrderedByName();

        return $this->render('pages/portfolio.html.twig', [
            'realisations' => $realisations,
            'categories' => $categories,
        ]);
    }

    #[Route('/a-propos', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('pages/about.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('pages/contact.html.twig');
    }

    #[Route('/faq', name: 'app_faq')]
    public function faq(): Response
    {
        return $this->render('pages/faq.html.twig');
    }

    #[Route('/devis', name: 'app_quote')]
    public function quote(): Response
    {
        return $this->render('pages/quote.html.twig');
    }
}
