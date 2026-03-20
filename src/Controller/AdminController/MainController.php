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
 * @Route("/admin", name="admin_")
 * @IsGranted("ROLE_ADMIN")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function home(): Response
    {
        return $this->render('admin/home.html.twig', [
        ]);
    }
}