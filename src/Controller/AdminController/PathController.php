<?php
namespace App\Controller\AdminController;

use App\Entity\Path;
use App\Form\PathType;
use App\Form\Handler\PathHandler;
use App\Repository\PathRepository;
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
 * Class PathController
 * @package App\Controller\AdminController
 * @Route("/admin/path", name="admin_path_")
 * @IsGranted("ROLE_ADMIN")
 */
class PathController extends AbstractController
{
    /**
     * @Route("/edit/{id}", name="edit")
     * @param Request $request
     * @param Path $path
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, Path $path, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PathType::class, $path);
        $form->handleRequest($request);
        $midwife = $path->getMidwife();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_midwife_edit', ['id'=>$midwife->getId()]);
        }
        return $this->render('admin/crud/_form.html.twig', [
            'el' => $path,
            'route'=> 'admin_path',
            'form' => $form->createView(),
            'button_label' => 'Mettre à jour',
            'title' => $path->getCity(),
            'breadcrumb'=>[
                [
                    'route'=>'admin_midwife_edit',
                    'params'=>["id"=>$midwife->getId()],
                    'text'=>$midwife->getFirstname().' '.$midwife->getLastname()
                ],
                [
                    'text'=>$path->getCity()
                ]
            ],
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param Path $path
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request,Path $path, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$path->getId(), $request->request->get('_token'))) {
            $entityManager->remove($path);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_midwife_edit', ['id'=>$path->getMidwife()->getId()]);
    }
}