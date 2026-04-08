<?php

namespace App\Controller\AdminController;

use App\Entity\InformationPage;
use App\Form\InformationPageType;
use App\Repository\InformationPageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[\Symfony\Component\Routing\Attribute\Route(path: '/admin/infos-utiles', name: 'admin_information_page_')]
#[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_ADMIN')]
class InformationPageController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route(path: '/edit', name: 'edit')]
    public function edit(Request $request, InformationPageRepository $informationPageRepository, EntityManagerInterface $entityManager): Response
    {
        $informationPage = $informationPageRepository->findOneBy([]);
        assert($informationPage instanceof InformationPage);

        $form = $this->createForm(InformationPageType::class, $informationPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_information_page_edit', ['id' => $informationPage->getId()]);
        }

        return $this->render('admin/crud/_form.html.twig', [
            'el' => $informationPage,
            'route' => 'admin_information_page',
            'form' => $form,
            'button_label' => 'Mettre à jour',
            'title' => 'Edition',
            'breadcrumb' => [
                ['route' => 'admin_information_page_edit', 'params' => ['id' => 1], 'text' => 'Informations utiles'],
                ['text' => 'édition'],
            ],
        ]);
    }
}
