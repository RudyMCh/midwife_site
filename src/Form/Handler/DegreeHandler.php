<?php
namespace App\Form\Handler;

use App\Entity\Degree;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

class DegreeHandler extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly \Doctrine\Persistence\ManagerRegistry $managerRegistry)
    {
    }

    public function new(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $degree = $form->getData();
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->persist($degree);
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

    public function delete(Degree $degree, Request $request): void
    {
        if ($this->isCsrfTokenValid('delete'.$degree->getId(), $request->request->getString('_token'))) {
            $this->entityManager->remove($degree);
            $this->entityManager->flush();
        }
    }
}
