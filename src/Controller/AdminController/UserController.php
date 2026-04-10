<?php

namespace App\Controller\AdminController;

use App\Entity\User;
use App\Form\Handler\UserHandler;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[\Symfony\Component\Routing\Attribute\Route(path: '/admin/user', name: 'admin_user_')]
class UserController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $els = $paginator->paginate(
            $userRepository->createQueryBuilder('a')->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/crud/index.html.twig', [
            'els' => $els,
            'paginator' => false,
            'search' => false,
            'class' => User::class,
            'route' => 'admin_user',
            'breadcrumb' => [
                ['text' => 'tous les éléments'],
            ],
            'fields' => [
                'Id' => 'Id',
                'Email' => 'Email',
                'Username' => 'Username',
                'Roles' => 'Roles',
                'Prénom' => 'Firstname',
                'Nom' => 'Lastname',
            ],
            'title' => 'Tous les élements',
            'add_button_label' => 'Ajouter un élément',
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserHandler $userHandler): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        if ($userHandler->new($form, $request)) {
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/crud/_form.html.twig', [
            'form' => $form,
            'el' => $user,
            'button_label' => 'Créer',
            'route' => 'admin_user',
            'title' => 'Ajouter un élément',
            'breadcrumb' => [
                ['route' => 'admin_user_index', 'text' => 'tous les éléments'],
                ['text' => 'ajouter un élément'],
            ],
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/edit/{id}', name: 'edit')]
    public function edit(Request $request, User $user, UserHandler $userHandler): Response
    {
        $form = $this->createForm(UserType::class, $user);
        if ($userHandler->edit($form, $request)) {
            return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
        }

        return $this->render('admin/crud/_form.html.twig', [
            'el' => $user,
            'route' => 'admin_user',
            'form' => $form,
            'button_label' => 'Mettre à jour',
            'title' => 'Edition',
            'breadcrumb' => [
                ['route' => 'admin_user_index', 'text' => 'users'],
                ['text' => 'édition'],
            ],
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
