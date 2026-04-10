<?php

namespace App\Controller\AdminController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MainController.
 */
#[\Symfony\Component\Routing\Attribute\Route(path: '/admin', name: 'admin_')]
#[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_ADMIN')]
class MainController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route(path: '/', name: 'home')]
    public function home(): Response
    {
        return $this->render('admin/home.html.twig', [
        ]);
    }
}
