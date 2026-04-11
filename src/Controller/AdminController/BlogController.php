<?php

namespace App\Controller\AdminController;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\Handler\ArticleHandler;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/blog', name: 'admin_blog_')]
#[IsGranted('ROLE_ADMIN')]
class BlogController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $articleRepository->search($request->query->get('search'));
        $els = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/crud/index.html.twig', [
            'els' => $els,
            'paginator' => true,
            'search' => true,
            'class' => Article::class,
            'route' => 'admin_blog',
            'breadcrumb' => [
                ['text' => 'Blog — tous les articles'],
            ],
            'fields' => [
                'Id' => 'Id',
                'Titre' => 'Title',
                'Publié' => 'IsPublished',
                'Date publication' => 'PublishedAt',
                'Auteure' => 'Author',
            ],
            'title' => 'Blog — tous les articles',
            'add_button_label' => 'Rédiger un article',
        ]);
    }

    #[Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleHandler $articleHandler): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        if ($articleHandler->new($form, $request)) {
            $this->addFlash('success', 'Article créé avec succès.');

            return $this->redirectToRoute('admin_blog_index');
        }

        return $this->render('admin/blog/_form.html.twig', [
            'form' => $form,
            'el' => $article,
            'button_label' => 'Créer l\'article',
            'route' => 'admin_blog',
            'title' => 'Rédiger un article',
            'breadcrumb' => [
                ['route' => 'admin_blog_index', 'text' => 'Blog'],
                ['text' => 'Nouvel article'],
            ],
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleHandler $articleHandler): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        if ($articleHandler->edit($form, $request)) {
            $this->addFlash('success', 'Article mis à jour.');

            return $this->redirectToRoute('admin_blog_edit', ['id' => $article->getId()]);
        }

        return $this->render('admin/blog/_form.html.twig', [
            'form' => $form,
            'el' => $article,
            'button_label' => 'Mettre à jour',
            'route' => 'admin_blog',
            'title' => $article->getTitle(),
            'breadcrumb' => [
                ['route' => 'admin_blog_index', 'text' => 'Blog'],
                ['text' => $article->getTitle()],
            ],
        ]);
    }

    #[Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, Article $article, ArticleHandler $articleHandler): Response
    {
        $articleHandler->delete($article, $request);

        return $this->redirectToRoute('admin_blog_index');
    }
}
