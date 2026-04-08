<?php

namespace App\Controller\AdminController;

use App\Entity\Office;
use App\Form\OfficeType;
use App\Repository\OfficeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[\Symfony\Component\Routing\Attribute\Route(path: '/admin/office', name: 'admin_office_')]
#[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_ADMIN')]
class OfficeController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route(path: '/edit', name: 'edit')]
    public function edit(Request $request, OfficeRepository $officeRepository, EntityManagerInterface $entityManager): Response
    {
        $office = $officeRepository->findOneBy([]);
        assert($office instanceof Office);

        $form = $this->createForm(OfficeType::class, $office);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_office_edit', ['id' => 1]);
        }

        return $this->render('admin/crud/_form.html.twig', [
            'el' => $office,
            'route' => 'admin_office',
            'form' => $form,
            'button_label' => 'Mettre à jour',
            'title' => 'Cabinet',
            'breadcrumb' => [
                ['text' => 'Cabinet'],
            ],
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, Office $office, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$office->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($office);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_office_edit', ['id' => 1]);
    }
}
