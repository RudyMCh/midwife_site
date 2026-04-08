<?php

namespace App\Controller\FrontController;

use App\Entity\InformationPage;
use App\Repository\InformationPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InformationController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route(path: '/informations-utiles', name: 'informations_utiles')]
    public function show(InformationPageRepository $informationPageRepository): Response
    {
        $info = $informationPageRepository->findAll();
        $info = $info[0];
        return $this->render('front/informationUtiles.html.twig', [
            'information'=>$info
        ]);
    }
}