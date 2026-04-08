<?php
namespace App\Controller\AdminController;

use App\Entity\Domain;
use App\Entity\Service;
use App\Form\ServiceType;
use App\Form\Handler\ServiceHandler;
use App\Repository\ServiceRepository;
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
 * Class ServiceController
 * @package App\Controller\AdminController
 */
#[\Symfony\Component\Routing\Attribute\Route(path: '/admin/service', name: 'admin_service_')]
class ServiceController extends AbstractController
{

    #[\Symfony\Component\Routing\Attribute\Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $serviceRepository->search($request->query->get('search'));
        $els = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('admin/crud/index.html.twig', [
            'els'=>$els,
            'parent'=>'Domain',
            'parentClass'=> Domain::class,
            'paginator'=>true,
            'search'=>true,
            'class'=> Service::class,
            'route'=> 'admin_service',
            'breadcrumb'=>[
                [
                    'text'=>'Toutes les prestations'
                ]
            ],
            'fields' => [
                'Id' => 'Id',
                'Nom' => 'Name',
                'Domaine'=> 'Domain',
                'Description' => 'Description',
                'Position'=>'Position'

            ],
            'title' => 'Toutes les prestations',
            'add_button_label'=>'Ajouter une prestation'
        ]);
    }

    /**
     * @param Request $request
     * @param ServiceHandler $serviceHandler
     * @return Response
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, ServiceHandler $serviceHandler): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        if ($serviceHandler->new($form, $request)) {
            return $this->redirectToRoute('admin_service_index');
        }

        return $this->render('admin/crud/_form.html.twig', [
            'form'=>$form,
            'el'=>$service,
            'button_label'=>'Créer',
            'route'=>'admin_service',
            'title'=>'Ajouter une prestation',
            'breadcrumb'=>[
                [
                    'route'=>'admin_service_index',
                    'text'=>'Toutes les prestations'
                ],
                [
                    'text'=>'Ajouter une prestation'
                ]
            ],

        ]);
    }

    /**
     * @param Request $request
     * @param Service $service
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/edit/{id}', name: 'edit')]
    public function edit(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_service_edit', ['id'=>$service->getId()]);
        }
        return $this->render('admin/crud/_form.html.twig', [
            'el' => $service,
            'route'=> 'admin_service',
            'form' => $form,
            'button_label' => 'Mettre à jour',
            'title' => $service->getName(),
            'breadcrumb'=>[
                [
                    'route'=>'admin_service_index',
                    'text'=>'Prestations'
                ],
                [
                    'text'=>$service->getName()
                ]
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Service $service
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request,Service $service, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $entityManager->remove($service);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_service_index');
    }
}