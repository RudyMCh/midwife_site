<?php

namespace App\Controller\FrontController;

use App\Entity\Midwife;
use App\Repository\DomainRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: 'sage-femme', name: 'midwife_')]
class MidwifeController extends AbstractController
{
    #[Route(path: '/{slug}', name: 'show')]
    #[Cache(public: true, maxage: 3600, mustRevalidate: true)]
    public function midwife(Midwife $midwife, DomainRepository $domainRepository): \Symfony\Component\HttpFoundation\Response
    {
        $metaTitle = $midwife->getMetaTitle()
            ?? sprintf('Sage-femme %s %s — %s', $midwife->getFirstname(), $midwife->getLastname(), 'Quetigny');

        return $this->render('front/midwife.html.twig', [
            'midwife' => $midwife,
            'domains' => $domainRepository->findAll(),
            'meta_title' => $metaTitle,
            'meta_description' => $midwife->getMetaDescription() ?? '',
        ]);
    }
}
