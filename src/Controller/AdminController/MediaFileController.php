<?php

namespace App\Controller\AdminController;

use App\Entity\MediaFile;
use App\Form\MediaFileMetaType;
use App\Repository\MediaFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/mediatheque', name: 'admin_media_file_')]
#[IsGranted('ROLE_ADMIN')]
class MediaFileController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(
        MediaFileRepository $repository,
        PaginatorInterface $paginator,
        Request $request,
    ): Response {
        $search = $request->query->getString('search');
        $query = $search !== ''
            ? $repository->createQueryBuilder('m')
                ->where('m.filename LIKE :term OR m.title LIKE :term OR m.alt LIKE :term')
                ->setParameter('term', '%'.$search.'%')
                ->orderBy('m.createdAt', 'DESC')
                ->getQuery()
            : $repository->createQueryBuilder('m')
                ->orderBy('m.createdAt', 'DESC')
                ->getQuery();

        $files = $paginator->paginate($query, $request->query->getInt('page', 1), 24);

        return $this->render('admin/media_file/index.html.twig', [
            'files' => $files,
            'search' => $search,
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MediaFile $mediaFile, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MediaFileMetaType::class, $mediaFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Fichier mis à jour.');

            return $this->redirectToRoute('admin_media_file_index');
        }

        return $this->render('admin/media_file/edit.html.twig', [
            'form' => $form,
            'file' => $mediaFile,
        ]);
    }

    #[Route(path: '/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        Request $request,
        MediaFile $mediaFile,
        EntityManagerInterface $em,
        #[Autowire('%kernel.project_dir%')]
        string $projectDir,
    ): Response {
        if (!$this->isCsrfTokenValid('delete'.$mediaFile->getId(), $request->request->getString('_token'))) {
            return $this->redirectToRoute('admin_media_file_index');
        }

        $uploadsPath = $projectDir.'/public'.$mediaFile->getPath();
        $thumbPath = $projectDir.'/public/thumbs/'.$mediaFile->getFilename();

        if (file_exists($uploadsPath)) {
            unlink($uploadsPath);
        }
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }

        $em->remove($mediaFile);
        $em->flush();

        $this->addFlash('success', 'Fichier supprimé.');

        return $this->redirectToRoute('admin_media_file_index');
    }
}
