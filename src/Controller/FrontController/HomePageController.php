<?php

namespace App\Controller\FrontController;

use App\Repository\DomainRepository;
use App\Repository\HomePageRepository;
use App\Repository\MidwifeRepository;
use App\Repository\OfficeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/', name: 'homepage_')]
class HomePageController extends AbstractController
{
    #[Route(path: '', name: 'homepage')]
    #[Cache(public: true, maxage: 3600, mustRevalidate: true)]
    public function homepage(
        MidwifeRepository $midwifeRepository,
        DomainRepository $domainRepository,
        HomePageRepository $homePageRepository,
        OfficeRepository $officeRepository,
    ): Response {
        $homePage = $homePageRepository->findAll()[0] ?? null;
        $office = $officeRepository->findAll()[0] ?? null;

        if (null === $homePage || null === $office) {
            throw $this->createNotFoundException('Données de la page d\'accueil manquantes.');
        }

        return $this->render('front/homepage.html.twig', [
            'midwives' => $midwifeRepository->findAll(),
            'domains' => $domainRepository->findAll(),
            'homepage' => $homePage,
            'office' => $office,
            'meta_title' => $homePage->getMetaTitle(),
            'meta_description' => $homePage->getMetaDescription(),
        ]);
    }
}
