<?php
namespace App\Controller\AdminController;

use App\Entity\User;
use App\Form\UserType;
use App\Form\Handler\UserHandler;
use App\Repository\UserRepository;
use App\Services\Tools;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class UserController
 * @package App\Controller\AdminController
 * @Route("/admin/user", name="admin_user_")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $els = $paginator->paginate(
            $userRepository->createQueryBuilder('a')->getQuery(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('admin/crud/index.html.twig', [
            'els'=>$els,
            'paginator'=>false,
            'search'=>false,
            'class'=> User::class,
            'route'=> 'admin_user',
            'breadcrumb'=>[
                [
                    'text'=>'tous les éléments'
                ]
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
            'add_button_label'=>'Ajouter un élément'
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @param Request $request
     * @param UserHandler $userHandler
     * @return Response
     */
    public function new(Request $request, UserHandler $userHandler): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        if ($userHandler->new($form, $request)) {
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/crud/_form.html.twig', [
            'form'=>$form->createView(),
            'el'=>$user,
            'button_label'=>'Créer',
            'route'=>'admin_user',
            'title'=>'Ajouter un élément',
            'breadcrumb'=>[
                [
                    'route'=>'admin_user_index',
                    'text'=>'tous les éléments'
                ],
                [
                    'text'=>'ajouter un élément'
                ]
            ],

        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @param Request $request
     * @param User $user
     * @param UserHandler $userHandler
     * @return Response
        */
    public function edit(Request $request, User $user, UserHandler $userHandler): Response
    {
        $form = $this->createForm(UserType::class, $user);
        if ($userHandler->edit($form, $request)) {
            return $this->redirectToRoute('admin_user_edit', ['id'=>$user->getId()]);
        }
        return $this->render('admin/crud/_form.html.twig', [
            'el' => $user,
            'route'=> 'admin_user',
            'form' => $form->createView(),
            'button_label' => 'Mettre à jour',
            'title' => 'Edition',
            'breadcrumb'=>[
                [
                    'route'=>'admin_user_index',
                    'text'=>'users'
                ],
                [
                    'text'=>'édition '
                ]
            ],
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request,User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_user_index');
    }
}