<?php
namespace App\Controller\AdminController;

use App\Entity\Degree;
use App\Form\DegreeType;
use App\Form\Handler\DegreeHandler;
use App\Repository\DegreeRepository;
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
 * Class DegreeController
 * @package App\Controller\AdminController
 * @Route("/admin/diplome", name="admin_degree_")
 * @IsGranted("ROLE_ADMIN")
 */
class DegreeController extends AbstractController
{
    /**
     * @Route("/edit/{id}", name="edit")
     * @param Request $request
     * @param Degree $degree
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, Degree $degree, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DegreeType::class, $degree);
        $form->handleRequest($request);
        $midwife = $degree->getMidwife();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_midwife_edit', ['id'=>$midwife->getId()]);
        }
        return $this->render('admin/crud/_form.html.twig', [
            'el' => $degree,
            'route'=> 'admin_degree',
            'form' => $form->createView(),
            'button_label' => 'Mettre à jour',
            'title' => $degree->getTitle(),
            'breadcrumb'=>[
                [
                    'route'=>'admin_midwife_edit',
                    'params'=>["id"=>$midwife->getId()],
                    'text'=>$midwife->getFirstname().' '.$midwife->getLastname()
                ],
                [
                    'text'=>$degree->getTitle()
                ]
            ],
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param Degree $degree
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request,Degree $degree, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$degree->getId(), $request->request->get('_token'))) {
            $entityManager->remove($degree);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_midwife_edit', ['id'=>$degree->getMidwife()->getId()]);
    }
}