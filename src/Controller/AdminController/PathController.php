<?php

namespace App\Controller\AdminController;

use App\Entity\Path;
use App\Form\PathType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[\Symfony\Component\Routing\Attribute\Route(path: '/admin/path', name: 'admin_path_')]
#[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_ADMIN')]
class PathController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route(path: '/edit/{id}', name: 'edit')]
    public function edit(Request $request, Path $path, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PathType::class, $path);
        $form->handleRequest($request);
        $midwife = $path->getMidwife();
        assert(null !== $midwife);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_midwife_edit', ['id' => $midwife->getId()]);
        }

        return $this->render('admin/crud/_form.html.twig', [
            'el' => $path,
            'route' => 'admin_path',
            'form' => $form,
            'button_label' => 'Mettre à jour',
            'title' => $path->getCity(),
            'breadcrumb' => [
                ['route' => 'admin_midwife_edit', 'params' => ['id' => $midwife->getId()], 'text' => $midwife->getFirstname().' '.$midwife->getLastname()],
                ['text' => $path->getCity()],
            ],
        ]);
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request, Path $path, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$path->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($path);
            $entityManager->flush();
        }

        $midwife = $path->getMidwife();
        assert(null !== $midwife);

        return $this->redirectToRoute('admin_midwife_edit', ['id' => $midwife->getId()]);
    }
}
