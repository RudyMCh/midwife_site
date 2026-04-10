<?php

namespace App\Controller\AdminController;

use App\Entity\Degree;
use App\Entity\Midwife;
use App\Entity\Path;
use App\Form\DegreeType;
use App\Form\Handler\MidwifeHandler;
use App\Form\MidwifeType;
use App\Form\PathType;
use App\Repository\MidwifeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[\Symfony\Component\Routing\Attribute\Route(path: '/admin/sage-femme', name: 'admin_midwife_')]
#[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_ADMIN')]
class MidwifeController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(MidwifeRepository $midwifeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $els = $paginator->paginate(
            $midwifeRepository->createQueryBuilder('a')->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/crud/index.html.twig', [
            'els' => $els,
            'paginator' => false,
            'search' => false,
            'class' => Midwife::class,
            'route' => 'admin_midwife',
            'breadcrumb' => [
                ['text' => 'tous les éléments'],
            ],
            'fields' => [
                'Id' => 'Id',
                'Nom' => 'Lastname',
                'Prenom' => 'Firstname',
            ],
            'title' => 'Tous les élements',
            'add_button_label' => 'Ajouter un élément',
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, MidwifeHandler $midwifeHandler): Response
    {
        $midwife = new Midwife();
        $form = $this->createForm(MidwifeType::class, $midwife);
        if ($midwifeHandler->new($form, $request)) {
            return $this->redirectToRoute('admin_midwife_index');
        }

        return $this->render('admin/crud/_form.html.twig', [
            'form' => $form,
            'el' => $midwife,
            'button_label' => 'Créer',
            'route' => 'admin_midwife',
            'title' => 'Ajouter un élément',
            'breadcrumb' => [
                ['route' => 'admin_midwife_index', 'text' => 'tous les éléments'],
                ['text' => 'ajouter un élément'],
            ],
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/edit/{id}', name: 'edit')]
    public function edit(Request $request, Midwife $midwife, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MidwifeType::class, $midwife);
        $form->handleRequest($request);
        $degree = new Degree();
        $degreeForm = $this->createForm(DegreeType::class, $degree);
        $degreeForm->handleRequest($request);
        $path = new Path();
        $pathForm = $this->createForm(PathType::class, $path);
        $pathForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('services')->getData() as $service) {
                $midwife->addService($service);
            }
            $entityManager->flush();

            return $this->redirectToRoute('admin_midwife_edit', ['id' => $midwife->getId()]);
        }

        if ($degreeForm->isSubmitted() && $degreeForm->isValid()) {
            $degree->setMidwife($midwife);
            $entityManager->persist($degree);
            $entityManager->flush();

            return $this->redirectToRoute('admin_midwife_edit', ['id' => $midwife->getId()]);
        }

        if ($pathForm->isSubmitted() && $pathForm->isValid()) {
            $path->setMidwife($midwife);
            $entityManager->persist($path);
            $entityManager->flush();

            return $this->redirectToRoute('admin_midwife_edit', ['id' => $midwife->getId()]);
        }

        return $this->render('admin/midwife/_form.html.twig', [
            'midwife' => $midwife,
            'route' => 'admin_midwife',
            'form' => $form,
            'degreeForm' => $degreeForm,
            'pathForm' => $pathForm,
            'button_label' => 'Mettre à jour',
            'title' => $midwife->getFirstname().' '.$midwife->getLastname(),
            'breadcrumb' => [
                ['route' => 'admin_midwife_index', 'text' => 'Sage femmes'],
                ['text' => $midwife->getFirstname().' '.$midwife->getLastname()],
            ],
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, Midwife $midwife, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$midwife->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($midwife);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_midwife_index');
    }
}
