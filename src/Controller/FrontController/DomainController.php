<?php
namespace App\Controller\FrontController;

use App\Entity\Domain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/domaine', name: 'domain_')]
class DomainController extends AbstractController
{
    #[Route(path: '/{slug}', name: 'show')]
    public function show(Domain $domain): Response
    {
        return $this->render('front/domain.html.twig', [
            "domain"=>$domain
        ]);
    }
}