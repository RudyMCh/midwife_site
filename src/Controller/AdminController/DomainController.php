<?php
namespace App\Controller\AdminController;

use App\Entity\Domain;
use App\Form\DomainType;
use App\Form\Handler\DomainHandler;
use App\Repository\DomainRepository;
use App\Services\Tools;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class DomainController
 * @package App\Controller\AdminController
 * @Route("/admin/domain", name="admin_domain_")
 * @IsGranted("ROLE_ADMIN")
 */
class DomainController extends AbstractController
{

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(DomainRepository $domainRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $els = $paginator->paginate(
            $domainRepository->createQueryBuilder('a')->getQuery(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('admin/crud/index.html.twig', [
            'els'=>$els,
            'paginator'=>false,
            'search'=>false,
            'class'=> Domain::class,
            'route'=> 'admin_domain',
            'breadcrumb'=>[
                [
                    'text'=>'tous les éléments'
                ]
            ],
            'fields' => [
                'Id' => 'Id',
                'Nom' => 'Name',
                "Prestations"=> "Services"

            ],
            'title' => 'Tous les élements',
            'add_button_label'=>'Ajouter un élément'
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @param Request $request
     * @param DomainHandler $domainHandler
     * @return Response
     */
    public function new(Request $request, DomainHandler $domainHandler): Response
    {
        $domain = new Domain();
        $form = $this->createForm(DomainType::class, $domain);
        if ($domainHandler->new($form, $request)) {
            return $this->redirectToRoute('admin_domain_index');
        }

        return $this->render('admin/crud/_form.html.twig', [
            'form'=>$form->createView(),
            'el'=>$domain,
            'button_label'=>'Créer',
            'route'=>'admin_domain',
            'title'=>'Ajouter un élément',
            'breadcrumb'=>[
                [
                    'route'=>'admin_domain_index',
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
     * @param Domain $domain
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, Domain $domain, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DomainType::class, $domain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_domain_edit', ['id'=>$domain->getId()]);
        }
        return $this->render('admin/crud/_form.html.twig', [
            'el' => $domain,
            'route'=> 'admin_domain',
            'form' => $form->createView(),
            'button_label' => 'Mettre à jour',
            'title' => 'Edition',
            'breadcrumb'=>[
                [
                    'route'=>'admin_domain_index',
                    'text'=>'domains'
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
     * @param Domain $domain
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request,Domain $domain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$domain->getId(), $request->request->get('_token'))) {
            $entityManager->remove($domain);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_domain_index');
    }
}