<?php
namespace App\Controller\FrontController;

use App\Entity\Domain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/domaine', name: 'domain_')]
class DomainController extends AbstractController
{
    #[Route(path: '/{slug}', name: 'show')]
    public function show(Domain $domain): Response
    {
        $metaTitle = $domain->getMetaTitle() ?? $domain->getName() . ' — Sages-femmes Quetigny';

        return $this->render('front/domain.html.twig', [
            'domain' => $domain,
            'meta_title' => $metaTitle,
            'meta_description' => $domain->getMetaDescription() ?? '',
        ]);
    }
}
