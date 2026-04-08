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
 */
#[\Symfony\Component\Routing\Attribute\Route(path: '/admin', name: 'admin_')]
#[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_ADMIN')]
class MainController extends AbstractController
{
    /**
     * @return Response
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/', name: 'home')]
    public function home(): Response
    {
        return $this->render('admin/home.html.twig', [
        ]);
    }
}