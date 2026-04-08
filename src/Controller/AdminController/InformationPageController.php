<?php
namespace App\Controller\AdminController;

use App\Entity\InformationPage;
use App\Form\InformationPageType;
use App\Form\Handler\InformationPageHandler;
use App\Repository\InformationPageRepository;
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
 * Class InformationPageController
 * @package App\Controller\AdminController
 * @IsGranted("ROLE_ADMIN")
 */
#[Route(path: '/admin/infos-utiles', name: 'admin_information_page_')]
class InformationPageController extends AbstractController
{
    /**
     * @param Request $request
     * @param InformationPageRepository $informationPageRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route(path: '/edit', name: 'edit')]
    public function edit(Request $request, InformationPageRepository $informationPageRepository, EntityManagerInterface $entityManager): Response
    {
        $informationPage = $informationPageRepository->findAll();
        $informationPage = $informationPage[0];
        $form = $this->createForm(InformationPageType::class, $informationPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_information_page_edit', ['id'=>$informationPage->getId()]);
        }
        return $this->render('admin/crud/_form.html.twig', [
            'el' => $informationPage,
            'route'=> 'admin_information_page',
            'form' => $form->createView(),
            'button_label' => 'Mettre à jour',
            'title' => 'Edition',
            'breadcrumb'=>[
                [
                    'route'=>'admin_information_page_edit',
                    'params'=>['id'=>1],
                    'text'=>'informationPages'
                ],
                [
                    'text'=>'édition '
                ]
            ],
        ]);
    }
}