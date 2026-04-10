<?php

namespace App\Controller\AdminController;

use App\Entity\Degree;
use App\Form\DegreeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[\Symfony\Component\Routing\Attribute\Route(path: '/admin/diplome', name: 'admin_degree_')]
#[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_ADMIN')]
class DegreeController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route(path: '/edit/{id}', name: 'edit')]
    public function edit(Request $request, Degree $degree, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DegreeType::class, $degree);
        $form->handleRequest($request);
        $midwife = $degree->getMidwife();
        assert(null !== $midwife);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_midwife_edit', ['id' => $midwife->getId()]);
        }

        return $this->render('admin/crud/_form.html.twig', [
            'el' => $degree,
            'route' => 'admin_degree',
            'form' => $form,
            'button_label' => 'Mettre à jour',
            'title' => $degree->getTitle(),
            'breadcrumb' => [
                ['route' => 'admin_midwife_edit', 'params' => ['id' => $midwife->getId()], 'text' => $midwife->getFirstname().' '.$midwife->getLastname()],
                ['text' => $degree->getTitle()],
            ],
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, Degree $degree, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$degree->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($degree);
            $entityManager->flush();
        }

        $midwife = $degree->getMidwife();
        assert(null !== $midwife);

        return $this->redirectToRoute('admin_midwife_edit', ['id' => $midwife->getId()]);
    }
}
