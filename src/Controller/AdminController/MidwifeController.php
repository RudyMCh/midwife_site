<?php
namespace App\Controller\AdminController;

use App\Entity\Degree;
use App\Entity\Midwife;
use App\Entity\Path;
use App\Form\DegreeType;
use App\Form\MidwifeType;
use App\Form\Handler\MidwifeHandler;
use App\Form\PathType;
use App\Repository\MidwifeRepository;
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
 * Class MidwifeController
 * @package App\Controller\AdminController
 * @Route("/admin/sage-femme", name="admin_midwife_")
 * @IsGranted("ROLE_ADMIN")
 */
class MidwifeController extends AbstractController
{

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(MidwifeRepository $midwifeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $els = $paginator->paginate(
            $midwifeRepository->createQueryBuilder('a')->getQuery(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('admin/crud/index.html.twig', [
            'els'=>$els,
            'paginator'=>false,
            'search'=>false,
            'class'=> Midwife::class,
            'route'=> 'admin_midwife',
            'breadcrumb'=>[
                [
                    'text'=>'tous les éléments'
                ]
            ],
            'fields' => [
                'Id' => 'Id',
                'Nom' => 'Lastname',
                'Prenom' => 'Firstname',

            ],
            'title' => 'Tous les élements',
            'add_button_label'=>'Ajouter un élément'
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     * @param Request $request
     * @param MidwifeHandler $midwifeHandler
     * @return Response
     */
    public function new(Request $request, MidwifeHandler $midwifeHandler): Response
    {
        $midwife = new Midwife();
        $form = $this->createForm(MidwifeType::class, $midwife);
        if ($midwifeHandler->new($form, $request)) {
            return $this->redirectToRoute('admin_midwife_index');
        }

        return $this->render('admin/crud/_form.html.twig', [
            'form'=>$form->createView(),
            'el'=>$midwife,
            'button_label'=>'Créer',
            'route'=>'admin_midwife',
            'title'=>'Ajouter un élément',
            'breadcrumb'=>[
                [
                    'route'=>'admin_midwife_index',
                    'text'=>'tous les éléments'
                ],
                [
                    'text'=>'ajouter un élément'
                ]
            ],

        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @param Request $request
     * @param Midwife $midwife
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, Midwife $midwife, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MidwifeType::class, $midwife);
        $form->handleRequest($request);
        $degree = new Degree();
        $degreeForm = $this->createForm(DegreeType::class, $degree);
        $degreeForm->handleRequest($request);
        $path = new Path();
        $pathForm = $this->createForm(PathType::class, $path);
        $pathForm->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $services = $form->get('services')->getData();
//            dd($services);
//            $servicesArray = [];
            foreach ($services as $service){
                $midwife->addService($service);
//                $servicesArray[]=$service;
            }
//            dd("fin");
            $entityManager->flush();

            return $this->redirectToRoute('admin_midwife_edit', ['id'=>$midwife->getId()]);
        }
        if($degreeForm->isSubmitted() && $degreeForm->isValid())
        {
            $degree->setMidwife($midwife);
            $entityManager->persist($degree);
            $entityManager->flush();
            return $this->redirectToRoute('admin_midwife_edit', ['id'=>$midwife->getId()]);
        }
        if($pathForm->isSubmitted() && $pathForm->isValid())
        {
            $path->setMidwife($midwife);
            $entityManager->persist($path);
            $entityManager->flush();
            return $this->redirectToRoute('admin_midwife_edit', ['id'=>$midwife->getId()]);
        }
        return $this->render('admin/midwife/_form.html.twig', [
            'midwife' => $midwife,
            'route'=> 'admin_midwife',
            'form' => $form->createView(),
            'degreeForm' => $degreeForm->createView(),
            'pathForm' => $pathForm->createView(),
            'button_label' => 'Mettre à jour',
            'title' => $midwife->getFirstname().' '.$midwife->getLastname(),
            'breadcrumb'=>[
                [
                    'route'=>'admin_midwife_index',
                    'text'=>'Sage femmes'
                ],
                [
                    'text'=>$midwife->getFirstname().' '.$midwife->getLastname()
                ]
            ],
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param Midwife $midwife
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request,Midwife $midwife, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$midwife->getId(), $request->request->get('_token')))
        {
            $entityManager->remove($midwife);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_midwife_index');
    }
}