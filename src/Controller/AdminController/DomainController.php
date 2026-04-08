<?php

namespace App\Controller\AdminController;

use App\Entity\Domain;
use App\Form\DomainType;
use App\Form\Handler\DomainHandler;
use App\Repository\DomainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;

#[\Symfony\Component\Routing\Attribute\Route(path: '/admin/domain', name: 'admin_domain_')]
#[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_ADMIN')]
class DomainController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(DomainRepository $domainRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $els = $paginator->paginate(
            $domainRepository->createQueryBuilder('a')->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/crud/index.html.twig', [
            'els' => $els,
            'paginator' => false,
            'search' => false,
            'class' => Domain::class,
            'route' => 'admin_domain',
            'breadcrumb' => [
                ['text' => 'tous les éléments'],
            ],
            'fields' => [
                'Id' => 'Id',
                'Nom' => 'Name',
                'Prestations' => 'Services',
            ],
            'title' => 'Tous les élements',
            'add_button_label' => 'Ajouter un élément',
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, DomainHandler $domainHandler): Response
    {
        $domain = new Domain();
        $form = $this->createForm(DomainType::class, $domain);
        if ($domainHandler->new($form, $request)) {
            return $this->redirectToRoute('admin_domain_index');
        }

        return $this->render('admin/crud/_form.html.twig', [
            'form' => $form,
            'el' => $domain,
            'button_label' => 'Créer',
            'route' => 'admin_domain',
            'title' => 'Ajouter un élément',
            'breadcrumb' => [
                ['route' => 'admin_domain_index', 'text' => 'tous les éléments'],
                ['text' => 'ajouter un élément'],
            ],
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/edit/{id}', name: 'edit')]
    public function edit(Request $request, Domain $domain, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DomainType::class, $domain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_domain_edit', ['id' => $domain->getId()]);
        }

        return $this->render('admin/crud/_form.html.twig', [
            'el' => $domain,
            'route' => 'admin_domain',
            'form' => $form,
            'button_label' => 'Mettre à jour',
            'title' => 'Edition',
            'breadcrumb' => [
                ['route' => 'admin_domain_index', 'text' => 'domains'],
                ['text' => 'édition'],
            ],
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, Domain $domain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$domain->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($domain);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_domain_index');
    }
}
