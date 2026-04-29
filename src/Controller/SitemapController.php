<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\RealisationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap', defaults: ['_format' => 'xml'])]
    public function index(
        ArticleRepository $articleRepository,
        RealisationRepository $realisationRepository,
    ): Response {
        $urls = [];
        $now = new \DateTimeImmutable();

        $staticPages = [
            ['route' => 'app_home', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['route' => 'app_offers', 'priority' => '0.9', 'changefreq' => 'monthly'],
            ['route' => 'app_about', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['route' => 'app_portfolio', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['route' => 'app_contact', 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['route' => 'app_blog', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['route' => 'app_parrainage', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['route' => 'app_faq', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => $this->generateUrl($page['route'], [], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => $now->format('Y-m-d'),
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ];
        }

        foreach ($articleRepository->findBy(['isPublished' => true]) as $article) {
            $urls[] = [
                'loc' => $this->generateUrl('app_blog_show', ['slug' => $article->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                'lastmod' => ($article->getUpdatedAt() ?? $article->getPublishedAt())?->format('Y-m-d') ?? $now->format('Y-m-d'),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        $response = new Response(
            $this->renderView('sitemap.xml.twig', ['urls' => $urls]),
            200,
            ['Content-Type' => 'application/xml']
        );

        return $response;
    }
}
