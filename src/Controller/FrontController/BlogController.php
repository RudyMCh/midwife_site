<?php

namespace App\Controller\FrontController;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/blog', name: 'blog_')]
class BlogController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $articleRepository->findPublishedQuery();
        $articles = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('front/blog/index.html.twig', [
            'articles' => $articles,
            'meta_title' => 'Blog — Sages-femmes Quetigny',
            'meta_description' => 'Retrouvez nos articles et conseils sur la grossesse, l\'accouchement, le post-partum et la santé des femmes.',
        ]);
    }

    #[Route(path: '/{slug}', name: 'show')]
    public function show(Article $article): Response
    {
        if (!$article->isPublished()) {
            throw $this->createNotFoundException('Cet article n\'est pas disponible.');
        }

        $metaTitle = $article->getMetaTitle() ?? $article->getTitle().' — Sages-femmes Quetigny';
        $metaDescription = $article->getMetaDescription() ?? $article->getExcerpt() ?? '';

        return $this->render('front/blog/show.html.twig', [
            'article' => $article,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
        ]);
    }
}
