<?php

namespace App\Controller\FrontController;

use App\Repository\InformationPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

class InformationController extends AbstractController
{
    #[Route(path: '/informations-utiles', name: 'informations_utiles')]
    #[Cache(public: true, maxage: 3600, mustRevalidate: true)]
    public function show(InformationPageRepository $informationPageRepository): Response
    {
        $info = $informationPageRepository->findAll();
        $info = $info[0];

        $metaTitle = $info->getMetaTitle() ?? 'Informations utiles — Sages-femmes Quetigny';

        return $this->render('front/informationUtiles.html.twig', [
            'information' => $info,
            'meta_title' => $metaTitle,
            'meta_description' => $info->getMetaDescription() ?? '',
        ]);
    }
}
