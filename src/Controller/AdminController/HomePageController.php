<?php
namespace App\Controller\AdminController;

use App\Entity\HomePage;
use App\Entity\Service;
use App\Form\HomePageType;
use App\Form\ServiceType;
use App\Form\Handler\ServiceHandler;
use App\Repository\HomePageRepository;
use App\Repository\ServiceRepository;
use App\Services\Tools;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ServiceController
 * @package App\Controller\AdminController
 * @IsGranted("ROLE_ADMIN")
 */
#[Route(path: '/admin/accueil', name: 'admin_homepage_')]
class HomePageController extends AbstractController
{
    /**
     * @param Request $request
     * @param HomePageRepository $homePageRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route(path: '/edit', name: 'edit')]
    public function edit(Request $request, HomePageRepository $homePageRepository, EntityManagerInterface $entityManager): Response
    {
        $homepage = $homePageRepository->findAll();
        $homepage = $homepage[0];
        $form = $this->createForm(HomePageType::class, $homepage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_homepage_edit', ['id'=>$homepage->getId()]);
        }
        return $this->render('admin/crud/_form.html.twig', [
            'el' => $homepage,
            'route'=> 'admin_homepage',
            'form' => $form->createView(),
            'button_label' => 'Mettre à jour',
            'title' => 'Page d\'accueil',
            'breadcrumb'=>[
                [
                    'text'=>'Page d\'accueil'
                ]
            ],
        ]);
    }
}