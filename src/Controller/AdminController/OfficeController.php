<?php
namespace App\Controller\AdminController;

use App\Entity\Office;
use App\Form\OfficeType;
use App\Form\Handler\OfficeHandler;
use App\Repository\OfficeRepository;
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
 * Class OfficeController
 * @package App\Controller\AdminController
 */
#[\Symfony\Component\Routing\Attribute\Route(path: '/admin/office', name: 'admin_office_')]
#[\Symfony\Component\Security\Http\Attribute\IsGranted('ROLE_ADMIN')]
class OfficeController extends AbstractController
{
    /**
     * @param Request $request
     * @param OfficeRepository $officeRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/edit', name: 'edit')]
    public function edit(Request $request, OfficeRepository $officeRepository, EntityManagerInterface $entityManager): Response
    {
        $office = $officeRepository->findAll();
        $office = $office[0];
        $form = $this->createForm(OfficeType::class, $office);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_office_edit', ['id'=>1]);
        }
        return $this->render('admin/crud/_form.html.twig', [
            'el' => $office,
            'route'=> 'admin_office',
            'form' => $form,
            'button_label' => 'Mettre à jour',
            'title' => 'Cabinet',
            'breadcrumb'=>[
                [
                    'text'=>'Cabinet '
                ]
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Office $office
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $request,Office $office, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$office->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($office);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_office_edit', ['id'=>1]);
    }
}