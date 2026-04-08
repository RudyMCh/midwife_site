<?php
namespace App\Form\Handler;

use App\Entity\Office;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

class OfficeHandler extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly \Doctrine\Persistence\ManagerRegistry $managerRegistry)
    {
    }

    public function new(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $office = $form->getData();
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->persist($office);
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

    public function delete(Office $office, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$office->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($office);
            $this->entityManager->flush();
        }
    }
}
