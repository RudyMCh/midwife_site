<?php
namespace App\Controller\AdminController;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class MainController
 * @package App\Controller\AdminController
 * @IsGranted("ROLE_ADMIN")
 */
#[Route(path: '/admin', name: 'admin_')]
class MainController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route(path: '/', name: 'home')]
    public function home(): Response
    {
        return $this->render('admin/home.html.twig', [
        ]);
    }
}