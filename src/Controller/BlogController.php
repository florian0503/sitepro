<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findPublished();

        return $this->render('pages/blog/index.html.twig', [
            'articles' => $articles,
            'featured' => $articles[0] ?? null,
            'rest' => array_slice($articles, 1),
        ]);
    }

    #[Route('/blog/{slug}', name: 'app_blog_show')]
    public function show(string $slug, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOnePublishedBySlug($slug);

        if (!$article) {
            throw $this->createNotFoundException('Article introuvable.');
        }

        $related = array_filter(
            $articleRepository->findPublished(),
            fn ($a) => $a->getId() !== $article->getId() && $a->getCategory() === $article->getCategory()
        );

        return $this->render('pages/blog/show.html.twig', [
            'article' => $article,
            'related' => array_slice(array_values($related), 0, 3),
        ]);
    }
}
