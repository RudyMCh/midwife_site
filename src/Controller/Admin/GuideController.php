<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/guide-seo', name: 'admin_guide_seo_')]
#[IsGranted('ROLE_ADMIN')]
class GuideController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/guide_seo/index.html.twig', [
            'title' => 'Guide SEO',
        ]);
    }
}
