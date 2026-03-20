<?php
namespace App\Form\Handler;

use App\Entity\Path;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

class PathHandler extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function new(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $path = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($path);
            $entityManager->flush();
            return true;
        }
        return false;
    }

    public function edit(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return true;
        }
        return false;
    }

    public function delete(Path $path, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$path->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($path);
            $this->entityManager->flush();
        }
    }
}
