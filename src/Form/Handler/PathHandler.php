<?php
namespace App\Form\Handler;

use App\Entity\Path;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

class PathHandler extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly \Doctrine\Persistence\ManagerRegistry $managerRegistry)
    {
    }

    public function new(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $path = $form->getData();
            $entityManager = $this->managerRegistry->getManager();
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

    public function delete(Path $path, Request $request): void
    {
        if ($this->isCsrfTokenValid('delete'.$path->getId(), $request->request->getString('_token'))) {
            $this->entityManager->remove($path);
            $this->entityManager->flush();
        }
    }
}
