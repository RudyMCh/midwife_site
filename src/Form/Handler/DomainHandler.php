<?php
namespace App\Form\Handler;

use App\Entity\Domain;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;

class DomainHandler extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly \Doctrine\Persistence\ManagerRegistry $managerRegistry)
    {
    }

    public function new(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $domain = $form->getData();
            $entityManager = $this->managerRegistry->getManager();
            $entityManager->persist($domain);
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

    public function delete(Domain $domain, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$domain->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($domain);
            $this->entityManager->flush();
        }
    }
}
