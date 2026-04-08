<?php
namespace App\Controller\FrontController;

use App\Entity\Midwife;
use App\Repository\DomainRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[\Symfony\Component\Routing\Attribute\Route(path: 'sage-femme', name: 'midwife_')]
class MidwifeController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route(path: '/{slug}', name: 'show')]
    public function midwife(Midwife $midwife, DomainRepository $domainRepository): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('front/midwife.html.twig', [
            'midwife'=>$midwife,
            'domains'=>$domainRepository->findAll(),
        ]);
    }
}