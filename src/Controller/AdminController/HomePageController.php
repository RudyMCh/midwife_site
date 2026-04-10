<?php

namespace App\Controller\AdminController;

use App\Entity\HomePage;
use App\Form\HomePageType;
use App\Repository\HomePageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[\Symfony\Component\Routing\Attribute\Route(path: '/admin/accueil', name: 'admin_homepage_')]
#[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_ADMIN')]
class HomePageController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route(path: '/edit', name: 'edit')]
    public function edit(Request $request, HomePageRepository $homePageRepository, EntityManagerInterface $entityManager): Response
    {
        $homepage = $homePageRepository->findOneBy([]);
        assert($homepage instanceof HomePage);
        $form = $this->createForm(HomePageType::class, $homepage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_homepage_edit', ['id' => $homepage->getId()]);
        }

        return $this->render('admin/crud/_form.html.twig', [
            'el' => $homepage,
            'route' => 'admin_homepage',
            'form' => $form,
            'button_label' => 'Mettre à jour',
            'title' => "Page d'accueil",
            'breadcrumb' => [
                ['text' => "Page d'accueil"],
            ],
        ]);
    }
}
